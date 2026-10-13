<?php

namespace App\Console\Commands;

use App\Models\ContentTranslation;
use App\Models\CouncilMeeting;
use App\Models\CouncilProject;
use App\Models\Department;
use App\Models\DistrictStatistic;
use App\Models\Document;
use App\Models\EditorialItem;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\HomepageSlide;
use App\Models\InvestmentOpportunity;
use App\Models\Media;
use App\Models\Official;
use App\Models\Page;
use App\Models\ProjectUpdate;
use App\Models\PublicContact;
use App\Models\Service;
use App\Models\Tender;
use App\Models\User;
use App\Models\Vacancy;
use App\Models\Ward;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Bootstrap translation coverage for Shona/Ndebele rows.
 *
 * --provider=copy embeds the English source (renders identically to the
 * fallback). --provider=mymemory machine-translates via the free MyMemory
 * API (en|sn for Shona, en|nr for Ndebele) so the site shows real Shona /
 * Ndebele wording. --refresh re-translates existing placeholder rows whose
 * value still equals the English source, preserving human-edited rows.
 * Council editors should still refine wording via TranslationFields forms.
 */
class FillMissingTranslations extends Command
{
    protected $signature = 'translations:fill-missing {--locale=all : sn, nd or all} {--limit=0 : max records per entity, 0 = unlimited} {--dry-run : report without writing} {--provider=copy : copy or mymemory} {--refresh : re-translate existing English-copy rows} {--email= : email for MyMemory quota}';

    protected $description = 'Fill missing sn/nd translation rows (copy English or machine-translate)';

    public const MT_PAIR = ['sn' => 'en|sn', 'nd' => 'en|nr'];

    /** @return array<class-string<Model>> */
    public static function entities(): array
    {
        return [
            Page::class,
            Service::class,
            EditorialItem::class,
            Department::class,
            CouncilProject::class,
            InvestmentOpportunity::class,
            Tender::class,
            Vacancy::class,
            Document::class,
            CouncilMeeting::class,
            Gallery::class,
            Ward::class,
            Official::class,
            HomepageSlide::class,
            DistrictStatistic::class,
            Media::class,
            ProjectUpdate::class,
            PublicContact::class,
            Faq::class,
        ];
    }

    public function handle(): int
    {
        $localeOption = strtolower((string) $this->option('locale'));
        $locales = $localeOption === 'all' ? ['sn', 'nd'] : [$localeOption];
        foreach ($locales as $locale) {
            if (! in_array($locale, ['sn', 'nd'], true)) {
                $this->error("Unsupported locale '{$locale}'. Use sn, nd or all.");

                return self::FAILURE;
            }
        }
        $provider = strtolower((string) $this->option('provider'));
        if (! in_array($provider, ['copy', 'mymemory'], true)) {
            $this->error("Unsupported provider '{$provider}'. Use copy or mymemory.");

            return self::FAILURE;
        }
        $limit = (int) $this->option('limit');
        $dryRun = (bool) $this->option('dry-run');
        $refresh = (bool) $this->option('refresh');
        $email = (string) $this->option('email');
        $editorId = User::where('status', 'active')->orderBy('id')->value('id');
        $translator = $provider === 'mymemory' ? new MyMemoryTranslator($email, $this) : null;

        $created = 0;
        foreach (self::entities() as $class) {
            $fields = $class::translatableFields();
            if ($fields === []) {
                continue;
            }
            $processed = 0;
            $class::query()->with('translations')->chunk(200, function ($records) use ($fields, $locales, $limit, &$processed, &$created, $dryRun, $refresh, $editorId, $translator): void {
                foreach ($records as $record) {
                    if ($limit > 0 && $processed >= $limit) {
                        return;
                    }
                    $processed++;
                    $existing = $record->translations->keyBy(fn ($t) => $t->locale.'.'.$t->field);
                    foreach ($locales as $locale) {
                        foreach ($fields as $field) {
                            $row = $existing->get($locale.'.'.$field);
                            if ($row instanceof ContentTranslation && ! $refresh) {
                                continue;
                            }
                            $english = self::englishTexts($record, $field);
                            if ($english === []) {
                                continue;
                            }
                            if ($row instanceof ContentTranslation && $refresh && ! self::isEnglishCopy($row->value, $english)) {
                                continue; // Human-edited row: leave it alone.
                            }
                            if ($translator instanceof MyMemoryTranslator) {
                                $translated = $translator->translateLines($english, $locale);
                                if ($translated === null) {
                                    $this->error('Translation quota finished or API unreachable. Re-run later to resume.');

                                    return;
                                }
                            } else {
                                $translated = $english;
                            }
                            $stored = self::encodeLines($record, $field, $translated);
                            if ($stored === null) {
                                continue;
                            }
                            $created++;
                            if ($dryRun) {
                                continue;
                            }
                            DB::table('content_translations')->updateOrInsert(
                                ['translatable_type' => $record->getMorphClass(), 'translatable_id' => $record->getKey(), 'locale' => $locale, 'field' => $field],
                                ['value' => $stored, 'updated_by' => $editorId, 'created_at' => now(), 'updated_at' => now()]
                            );
                        }
                    }
                }
            });
            $this->line(class_basename($class).": processed {$processed}");
        }

        $this->info(($dryRun ? 'Would write ' : 'Wrote ').$created." translation rows ({$provider}; ".implode(',', $locales).').');

        return self::SUCCESS;
    }

    /** @return list<string> */
    private static function englishTexts(Model $record, string $field): array
    {
        $english = $record->getAttribute($field);
        if (is_string($english)) {
            return trim($english) === '' ? [] : [$english];
        }
        if (! is_array($english) || $english === []) {
            return [];
        }
        if (isset($english[0]) && is_array($english[0]) && array_key_exists('text', $english[0])) {
            return array_values(array_filter(array_map(fn ($b): string => trim((string) ($b['text'] ?? '')), $english), fn ($t): bool => $t !== ''));
        }

        return array_values(array_filter(array_map(fn ($v): string => is_string($v) ? trim($v) : trim((string) json_encode($v)), $english), fn ($t): bool => $t !== ''));
    }

    /** @param  list<string>  $translated */
    private static function encodeLines(Model $record, string $field, array $translated): ?string
    {
        if ($translated === []) {
            return null;
        }
        if (is_string($record->getAttribute($field))) {
            return $translated[0];
        }

        return json_encode($translated, JSON_UNESCAPED_UNICODE);
    }

    /** @param  list<string>  $english */
    private static function isEnglishCopy(string $stored, array $english): bool
    {
        if (count($english) === 1 && ! str_starts_with(ltrim($stored), '[')) {
            return trim($stored) === trim($english[0]);
        }
        $decoded = json_decode($stored, true);

        return is_array($decoded) && array_values($decoded) === array_values($english);
    }
}

/**
 * Minimal MyMemory (https://mymemory.translated.net) client.
 * Returns null when the daily quota is finished or the API is unreachable
 * so the command can stop and resume later.
 */
class MyMemoryTranslator
{
    public function __construct(private string $email, private Command $output) {}

    /** @param  list<string>  $lines */
    public function translateLines(array $lines, string $locale): ?array
    {
        $pair = FillMissingTranslations::MT_PAIR[$locale] ?? null;
        if ($pair === null) {
            return null;
        }
        $out = [];
        foreach ($lines as $line) {
            $translated = $this->translateText($line, $pair);
            if ($translated === null) {
                return null;
            }
            $out[] = $translated;
        }

        return $out;
    }

    private function translateText(string $text, string $pair): ?string
    {
        if (trim($text) === '' || mb_strlen($text) < 2) {
            return $text;
        }
        // Keep proper nouns intact: skip strings without lowercase letters.
        if (! preg_match('/[a-z]/u', $text)) {
            return $text;
        }
        foreach (self::chunks($text) as $chunk) {
            $results[] = $this->request($chunk, $pair);
            if (end($results) === null) {
                return null;
            }
            usleep(1500000); // ~0.6 req/s to respect the free tier.
        }

        return implode(' ', array_filter($results ?? [])) ?: $text;
    }

    /** @return list<string> */
    private static function chunks(string $text): array
    {
        if (mb_strlen($text) <= 450) {
            return [$text];
        }
        $parts = preg_split('/(?<=[.!?])\s+/u', $text) ?: [$text];
        $chunks = [];
        $current = '';
        foreach ($parts as $part) {
            if (mb_strlen($current.' '.$part) > 450 && $current !== '') {
                $chunks[] = trim($current);
                $current = $part;
            } else {
                $current = trim($current.' '.$part);
            }
        }
        if (trim($current) !== '') {
            $chunks[] = trim($current);
        }

        return $chunks === [] ? [$text] : $chunks;
    }

    private function request(string $text, string $pair): ?string
    {
        for ($attempt = 0; $attempt < 4; $attempt++) {
            try {
                $response = Http::timeout(20)->get('https://api.mymemory.translated.net/get', array_filter([
                    'q' => $text,
                    'langpair' => $pair,
                    'de' => $this->email !== '' ? $this->email : null,
                ]));
            } catch (\Throwable $e) {
                $this->output->warn('MT request failed: '.$e->getMessage());

                return null;
            }
            if ($response->status() === 429) {
                $wait = 60 * ($attempt + 1);
                $this->output->warn("MT rate-limited (429). Waiting {$wait}s before retry...");
                sleep($wait);

                continue;
            }
            if (! $response->successful()) {
                $this->output->warn('MT HTTP '.$response->status());

                return null;
            }
            $data = $response->json();
            if (($data['quotaFinished'] ?? false) === true) {
                return null;
            }
            $translated = trim((string) ($data['responseData']['translatedText'] ?? ''));
            if ($translated === '' || str_contains($translated, 'MYMEMORY WARNING') || str_contains($translated, 'QUERY LENGTH LIMIT')) {
                $this->output->warn('MT quota/limit reached.');

                return null;
            }

            return $translated;
        }
        $this->output->warn('MT still rate-limited after retries.');

        return null;
    }
}
