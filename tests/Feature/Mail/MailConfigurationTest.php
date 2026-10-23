<?php

namespace Tests\Feature\Mail;

use App\Models\MailConfiguration;
use App\Services\Mail\MailConfigurationService;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MailConfigurationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SecuritySeeder::class);
    }

    public function test_credentials_are_encrypted_in_database_and_hidden_from_serialization(): void
    {
        $config = MailConfiguration::create([
            'name' => 'Secure Gmail Relay',
            'provider' => 'gmail',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'encryption' => 'tls',
            'username' => 'ict@mutokordc.org.zw',
            'password' => 'SuperSecretP@ssword123',
            'from_name' => 'Mutoko Rural District Council',
            'from_address' => 'info@mutokordc.org.zw',
            'is_active' => true,
        ]);

        // Raw database check: ensure password and username are encrypted and not stored as plaintext
        $raw = DB::table('mail_configurations')->where('id', $config->id)->first();
        $this->assertNotSame('SuperSecretP@ssword123', $raw->password);
        $this->assertNotSame('ict@mutokordc.org.zw', $raw->username);

        // Model attribute decryption
        $fresh = $config->fresh();
        $this->assertSame('SuperSecretP@ssword123', $fresh->password);
        $this->assertSame('ict@mutokordc.org.zw', $fresh->username);

        // Hidden from serialization / json
        $array = $fresh->toArray();
        $this->assertArrayNotHasKey('password', $array);
    }

    public function test_runtime_configuration_service_applies_settings_to_mail_config(): void
    {
        $config = MailConfiguration::create([
            'name' => 'Custom SMTP',
            'provider' => 'custom',
            'host' => 'mail.example.org',
            'port' => 2525,
            'encryption' => 'tls',
            'username' => 'user@example.org',
            'password' => 'secret',
            'from_name' => 'Mutoko Desk',
            'from_address' => 'desk@example.org',
            'is_active' => true,
            'mail_enabled' => true,
        ]);

        $service = app(MailConfigurationService::class);
        $service->clearCache();
        $service->applyRuntimeConfig($config);

        $this->assertSame('smtp', Config::get('mail.default'));
        $this->assertSame('mail.example.org', Config::get('mail.mailers.smtp.host'));
        $this->assertSame(2525, Config::get('mail.mailers.smtp.port'));
        $this->assertSame('desk@example.org', Config::get('mail.from.address'));
        $this->assertSame('Mutoko Desk', Config::get('mail.from.name'));
    }

    public function test_runtime_configuration_diverts_to_log_when_mail_disabled(): void
    {
        $config = MailConfiguration::create([
            'name' => 'Disabled SMTP',
            'provider' => 'custom',
            'host' => 'mail.example.org',
            'port' => 587,
            'encryption' => 'tls',
            'from_name' => 'Mutoko Desk',
            'from_address' => 'desk@example.org',
            'is_active' => true,
            'mail_enabled' => false,
        ]);

        $service = app(MailConfigurationService::class);
        $service->clearCache();
        $service->applyRuntimeConfig($config);

        $this->assertSame('log', Config::get('mail.default'));
    }

    public function test_only_one_configuration_remains_active_on_activate(): void
    {
        $config1 = MailConfiguration::create([
            'name' => 'Config 1',
            'provider' => 'gmail',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'encryption' => 'tls',
            'from_name' => 'Mutoko RDC',
            'from_address' => 'info@mutokordc.org.zw',
            'is_active' => true,
        ]);

        $config2 = MailConfiguration::create([
            'name' => 'Config 2',
            'provider' => 'cpanel',
            'host' => 'mail.mutokordc.org.zw',
            'port' => 465,
            'encryption' => 'ssl',
            'from_name' => 'Mutoko RDC',
            'from_address' => 'info@mutokordc.org.zw',
            'is_active' => false,
        ]);

        $config2->activate();

        $this->assertFalse((bool) $config1->fresh()->is_active);
        $this->assertTrue((bool) $config2->fresh()->is_active);
    }
}
