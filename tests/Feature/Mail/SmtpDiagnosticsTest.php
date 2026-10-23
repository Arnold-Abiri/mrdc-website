<?php

namespace Tests\Feature\Mail;

use App\Mail\SmtpTestMail;
use App\Models\MailConfiguration;
use App\Models\User;
use App\Services\Mail\SmtpDiagnosticsService;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SmtpDiagnosticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SecuritySeeder::class);
    }

    public function test_hostname_and_port_validation_blocks_unauthorized_ports(): void
    {
        $diagnostics = new SmtpDiagnosticsService;

        $valid = $diagnostics->validateHostAndPort('smtp.gmail.com', 587);
        $this->assertTrue($valid['valid']);

        $invalidPort = $diagnostics->validateHostAndPort('smtp.gmail.com', 8080);
        $this->assertFalse($invalidPort['valid']);
        $this->assertStringContainsString('not an authorized SMTP port', $invalidPort['error']);

        $emptyHost = $diagnostics->validateHostAndPort('', 587);
        $this->assertFalse($emptyHost['valid']);
    }

    public function test_error_sanitization_removes_sensitive_passwords_and_hashes(): void
    {
        $diagnostics = new SmtpDiagnosticsService;

        $rawError = 'Failed to connect: password=SuperSecretPassword123 with AUTH PLAIN dGVzdHVzZXI=';
        $clean = $diagnostics->sanitizeError($rawError);

        $this->assertStringNotContainsString('SuperSecretPassword123', $clean);
        $this->assertStringNotContainsString('dGVzdHVzZXI=', $clean);
        $this->assertStringContainsString('password=***', $clean);
    }

    public function test_send_test_email_dispatches_mailable_and_updates_status(): void
    {
        Mail::fake();

        $config = MailConfiguration::create([
            'name' => 'Test Unit Config',
            'provider' => 'gmail',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'encryption' => 'tls',
            'from_name' => 'Mutoko RDC',
            'from_address' => 'info@mutokordc.org.zw',
            'is_active' => true,
            'mail_enabled' => true,
        ]);

        $admin = User::factory()->create();

        $diagnostics = new SmtpDiagnosticsService;
        $result = $diagnostics->sendTestEmail($config, 'recipient@example.com', $admin);

        $this->assertTrue($result['success']);
        Mail::assertSent(SmtpTestMail::class);

        $fresh = $config->fresh();
        $this->assertSame('success', $fresh->last_tested_status);
        $this->assertNotNull($fresh->last_tested_at);
    }
}
