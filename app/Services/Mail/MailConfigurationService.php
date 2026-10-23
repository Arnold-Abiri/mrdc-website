<?php

namespace App\Services\Mail;

use App\Models\MailConfiguration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailConfigurationService
{
    private static ?MailConfiguration $resolvedConfig = null;

    private static bool $booted = false;

    /**
     * Retrieve the active mail configuration from the database or cache.
     * Returns null if no active database record exists or if the database is unreachable.
     */
    public function getActiveConfiguration(): ?MailConfiguration
    {
        if (self::$resolvedConfig !== null) {
            return self::$resolvedConfig;
        }

        try {
            $configId = Cache::remember('mail_configuration.active_id', 3600, function (): ?int {
                return MailConfiguration::query()->where('is_active', true)->value('id');
            });

            if ($configId) {
                self::$resolvedConfig = MailConfiguration::query()->find($configId);
            }
        } catch (Throwable) {
            self::$resolvedConfig = null;
        }

        return self::$resolvedConfig;
    }

    /**
     * Apply runtime mail configuration to Laravel config and mail transport.
     */
    public function applyRuntimeConfig(?MailConfiguration $config = null): void
    {
        $configuration = $config ?? $this->getActiveConfiguration();

        if ($configuration === null) {
            return;
        }

        if (! $configuration->mail_enabled) {
            // When mail is disabled, divert to array or log mailer to prevent outbound connections
            Config::set('mail.default', 'log');
            $this->purgeMailer();

            return;
        }

        $scheme = match ($configuration->encryption) {
            'ssl' => 'smtps',
            default => 'smtp',
        };

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.scheme', $scheme);
        Config::set('mail.mailers.smtp.host', $configuration->host);
        Config::set('mail.mailers.smtp.port', $configuration->port);
        Config::set('mail.mailers.smtp.encryption', $configuration->encryption === 'none' ? null : $configuration->encryption);
        Config::set('mail.mailers.smtp.username', $configuration->username);
        Config::set('mail.mailers.smtp.password', $configuration->password);
        Config::set('mail.mailers.smtp.timeout', 15);

        if ($configuration->from_address) {
            Config::set('mail.from.address', $configuration->from_address);
        }
        if ($configuration->from_name) {
            Config::set('mail.from.name', $configuration->from_name);
        }

        $this->purgeMailer();
    }

    /**
     * Purge cached mail transport instances in Laravel to ensure fresh runtime parameters take effect.
     */
    public function purgeMailer(): void
    {
        try {
            Mail::purge('smtp');
            Mail::purge('log');
            Mail::purge('array');
            if (app()->bound('mailer')) {
                app()->forgetInstance('mailer');
            }
        } catch (Throwable) {
            // Silence if mailer was not instantiated yet
        }
    }

    /**
     * Clear cached active configuration so changes take effect immediately across workers and requests.
     */
    public function clearCache(): void
    {
        self::$resolvedConfig = null;
        Cache::forget('mail_configuration.active_id');
        $this->purgeMailer();
    }

    /**
     * Boot runtime configuration once per lifecycle.
     */
    public function boot(): void
    {
        if (self::$booted) {
            return;
        }

        self::$booted = true;
        $this->applyRuntimeConfig();
    }
}
