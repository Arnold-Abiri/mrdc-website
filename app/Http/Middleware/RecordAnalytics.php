<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RecordAnalytics
{
    private const BOT_PATTERN = '/bot|crawl|spider|slurp|mediapartners|baidu|yandex|sogou|exabot|facebot|ia_archiver|facebookexternalhit|linkedinbot|embedly|quora|outbrain|pinterest|slackbot|telegrambot|whatsapp|discordbot|uptime|healthcheck|pingdom|kube-probe|prometheus|nagios|zabbix|newrelic|datadog/i';

    private const SEARCH_HOSTS = ['google', 'bing', 'duckduckgo', 'yahoo', 'brave', 'ecosia', 'yandex', 'baidu', 'startpage'];

    private const SOCIAL_HOSTS = ['facebook', 'twitter', 'x.com', 'instagram', 'linkedin', 'youtube', 'tiktok', 'whatsapp', 'telegram', 'snapchat', 'pinterest', 'threads'];

    private const CONTENT_ROUTES = [
        'tenders.show' => 'tender',
        'vacancies.show' => 'vacancy',
        'news.show' => 'news',
        'notices.show' => 'notice',
        'services.show' => 'service',
        'documents.show' => 'document',
        'projects.show' => 'project',
        'investment.show' => 'investment',
        'meetings.show' => 'meeting',
        'pages.show' => 'page',
        'officials.show' => 'official',
        'wards.show' => 'ward',
        'departments.public.show' => 'department',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        try {
            $this->record($request, $response);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    private function record(Request $request, Response $response): void
    {
        if (! $request->isMethod('get') || $response->getStatusCode() !== 200) {
            return;
        }
        $route = $request->route();
        if (! $route instanceof Route) {
            return;
        }
        $name = (string) $route->getName();
        if ($name === '' || in_array($name, ['documents.download', 'managed-media.show'], true)) {
            return;
        }
        $agent = (string) $request->header('User-Agent', '');
        if ($agent !== '' && preg_match(self::BOT_PATTERN, $agent) === 1) {
            return;
        }
        $sessionId = $request->hasSession() ? $request->session()->getId() : null;
        if ($sessionId === null || $sessionId === '') {
            return;
        }
        $event = isset(self::CONTENT_ROUTES[$name]) ? 'content_view' : 'page_view';
        $subject = null;
        foreach (['slug', 'meeting', 'department', 'media'] as $param) {
            $value = $route->parameter($param);
            if (is_scalar($value)) {
                $subject = (string) $value;
                break;
            }
        }
        [$category, $domain] = $this->classifyReferrer((string) $request->headers->get('referer', ''), (string) $request->getHost());

        DB::table('analytics_events')->insert([
            'event_type' => $event,
            'route' => $name,
            'locale' => is_string($route->parameter('locale')) ? $route->parameter('locale') : null,
            'subject_type' => self::CONTENT_ROUTES[$name] ?? null,
            'subject' => $subject,
            'visitor_key' => hash('sha256', config('app.key').'|'.$sessionId.'|'.today()->toDateString()),
            'referrer_category' => $category,
            'referrer_domain' => $domain,
            'created_at' => now(),
        ]);
    }

    /**
     * @return array{0: string, 1: ?string}
     */
    public function classifyReferrer(string $referer, string $host): array
    {
        if ($referer === '') {
            return ['direct', null];
        }
        $refererHost = strtolower((string) parse_url($referer, PHP_URL_HOST));
        if ($refererHost === '') {
            return ['other', null];
        }
        $domain = preg_replace('/^www\./', '', $refererHost) ?? $refererHost;
        if ($domain === strtolower($host) || str_ends_with($domain, '.'.strtolower($host))) {
            return ['internal', $domain];
        }
        $labels = explode('.', $domain);
        while (count($labels) > 2 && in_array($labels[0], ['www', 'm', 'mobile', 'search'], true)) {
            array_shift($labels);
        }
        $normalized = implode('.', $labels);
        $registrable = $labels[0];
        if (in_array($registrable, self::SEARCH_HOSTS, true)) {
            return ['search', $normalized];
        }
        if (in_array($registrable, self::SOCIAL_HOSTS, true) || in_array($normalized, self::SOCIAL_HOSTS, true)) {
            return ['social', $normalized];
        }

        return ['external', $domain];
    }
}
