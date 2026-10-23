<?php

namespace App\Filament\Pages;

use App\Domain\Identity\AuditWriter;
use App\Filament\Clusters\Administration\AdministrationCluster;
use App\Models\MailConfiguration;
use App\Services\Mail\MailConfigurationService;
use App\Services\Mail\SmtpDiagnosticsService;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page as FilamentPage;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

/**
 * @property-read Schema $form
 */
class EmailSettings extends FilamentPage
{
    protected static ?string $cluster = AdministrationCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'Email & Notifications';

    protected static \UnitEnum|string|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'SMTP & Email Settings';

    protected string $view = 'filament.pages.email-settings';

    /**
     * Form state for SMTP settings.
     *
     * @var array<string, mixed>
     */
    public array $formData = [
        'id' => null,
        'name' => 'Production SMTP',
        'provider' => 'gmail',
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls',
        'username' => '',
        'password' => '',
        'from_name' => 'Mutoko Rural District Council',
        'from_address' => 'info@mutokordc.org.zw',
        'reply_to_address' => '',
        'mail_enabled' => true,
    ];

    public string $testRecipient = '';

    /**
     * Diagnostics result details.
     *
     * @var array<string, mixed>|null
     */
    public ?array $diagnosticResult = null;

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('mail_settings.view');
    }

    public function mount(): void
    {
        $this->loadActiveOrLatestConfiguration();
        $this->form->fill($this->formData);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('formData');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('SMTP Server & Provider')
                ->description('Configure the outgoing mail provider and transport security settings.')
                ->schema([
                    TextInput::make('name')
                        ->label('Configuration Name')
                        ->placeholder('e.g., Primary Gmail SMTP')
                        ->required()
                        ->maxLength(100),

                    Select::make('provider')
                        ->label('Provider Preset')
                        ->options([
                            'gmail' => 'Gmail / Google Workspace',
                            'cpanel' => 'cPanel Webmail (mail.domain)',
                            'microsoft365' => 'Microsoft 365 / Outlook',
                            'custom' => 'Custom SMTP Server',
                        ])
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (string $state, Set $set): void {
                            match ($state) {
                                'gmail' => [
                                    $set('host', 'smtp.gmail.com'),
                                    $set('port', 587),
                                    $set('encryption', 'tls'),
                                ],
                                'cpanel' => [
                                    $set('host', 'mail.mutokordc.org.zw'),
                                    $set('port', 465),
                                    $set('encryption', 'ssl'),
                                ],
                                'microsoft365' => [
                                    $set('host', 'smtp.office365.com'),
                                    $set('port', 587),
                                    $set('encryption', 'tls'),
                                ],
                                default => null,
                            };
                        }),

                    TextInput::make('host')
                        ->label('SMTP Hostname')
                        ->placeholder('smtp.gmail.com')
                        ->required()
                        ->maxLength(255),

                    Grid::make(2)->schema([
                        TextInput::make('port')
                            ->label('SMTP Port')
                            ->numeric()
                            ->required()
                            ->default(587)
                            ->helperText('Standard ports: 587 (TLS), 465 (SSL), 25 or 2525 (Plain)'),

                        Select::make('encryption')
                            ->label('Transport Security')
                            ->options([
                                'tls' => 'STARTTLS (Port 587)',
                                'ssl' => 'SMTPS / SSL (Port 465)',
                                'none' => 'None / Plain (Port 25/2525)',
                            ])
                            ->required()
                            ->default('tls'),
                    ]),

                    TextInput::make('username')
                        ->label('SMTP Username / Account')
                        ->placeholder('user@mutokordc.org.zw')
                        ->maxLength(255)
                        ->helperText('Full email address or username required by your provider.'),

                    TextInput::make('password')
                        ->label('SMTP Password / App Secret')
                        ->password()
                        ->revealable()
                        ->placeholder('Leave blank to keep current encrypted password')
                        ->helperText('Stored securely with AES-256-CBC. Only enter a new password if updating.')
                        ->dehydrated(fn ($state) => filled($state)),
                ])->columns(2),

            Section::make('Sender Identity & Routing')
                ->description('Specify the default sender details attached to citizen notifications and system emails.')
                ->schema([
                    TextInput::make('from_name')
                        ->label('Sender Name')
                        ->required()
                        ->maxLength(150),

                    TextInput::make('from_address')
                        ->label('Sender Email Address')
                        ->email()
                        ->required()
                        ->maxLength(255),

                    TextInput::make('reply_to_address')
                        ->label('Reply-To Email Address (Optional)')
                        ->email()
                        ->maxLength(255)
                        ->helperText('Replies to system notifications will be routed here.'),

                    Toggle::make('mail_enabled')
                        ->label('Enable Outbound Email Dispatch')
                        ->helperText('When disabled, outgoing emails are captured into logs and no external connections are made.')
                        ->default(true),
                ])->columns(2),
        ]);
    }

    public function loadActiveOrLatestConfiguration(): void
    {
        $config = MailConfiguration::query()->where('is_active', true)->first()
            ?? MailConfiguration::query()->latest()->first();

        if ($config) {
            $this->formData = [
                'id' => $config->id,
                'name' => $config->name,
                'provider' => $config->provider,
                'host' => $config->host,
                'port' => $config->port,
                'encryption' => $config->encryption,
                'username' => $config->username ?? '',
                'password' => '', // Never expose stored password
                'from_name' => $config->from_name,
                'from_address' => $config->from_address,
                'reply_to_address' => $config->reply_to_address ?? '',
                'mail_enabled' => (bool) $config->mail_enabled,
            ];
        } else {
            // Populate defaults from fallback .env / config
            $this->formData['host'] = config('mail.mailers.smtp.host', 'smtp.gmail.com') ?: 'smtp.gmail.com';
            $this->formData['port'] = (int) config('mail.mailers.smtp.port', 587) ?: 587;
            $this->formData['from_address'] = config('mail.from.address', 'info@mutokordc.org.zw') ?: 'info@mutokordc.org.zw';
            $this->formData['from_name'] = config('mail.from.name', 'Mutoko Rural District Council') ?: 'Mutoko Rural District Council';
        }

        if (empty($this->testRecipient)) {
            $user = auth()->user();
            $this->testRecipient = $user ? $user->email : 'ict@mutokordc.org.zw';
        }
    }

    public function applyPreset(string $preset): void
    {
        $this->formData['provider'] = $preset;

        switch ($preset) {
            case 'gmail':
                $this->formData['host'] = 'smtp.gmail.com';
                $this->formData['port'] = 587;
                $this->formData['encryption'] = 'tls';
                break;
            case 'cpanel':
                $this->formData['host'] = 'mail.mutokordc.org.zw';
                $this->formData['port'] = 465;
                $this->formData['encryption'] = 'ssl';
                break;
            case 'microsoft365':
                $this->formData['host'] = 'smtp.office365.com';
                $this->formData['port'] = 587;
                $this->formData['encryption'] = 'tls';
                break;
            default:
                break;
        }

        $this->form->fill($this->formData);

        Notification::make()
            ->title('Preset Applied')
            ->body("Applied editable defaults for {$preset}. Verify your credentials before saving.")
            ->info()
            ->send();
    }

    public function saveDraft(): void
    {
        if (! auth()->user()?->can('mail_settings.update')) {
            Notification::make()->title('Unauthorized')->danger()->send();

            return;
        }

        $data = $this->form->getState();

        $configId = $this->formData['id'] ?? null;
        $config = $configId ? MailConfiguration::query()->find($configId) : new MailConfiguration;

        if (! $config) {
            $config = new MailConfiguration;
        }

        $config->name = $data['name'] ?? $this->formData['name'];
        $config->provider = $data['provider'] ?? $this->formData['provider'];
        $config->host = $data['host'] ?? $this->formData['host'];
        $config->port = (int) ($data['port'] ?? $this->formData['port']);
        $config->encryption = $data['encryption'] ?? $this->formData['encryption'];
        $config->from_name = $data['from_name'] ?? $this->formData['from_name'];
        $config->from_address = $data['from_address'] ?? $this->formData['from_address'];
        $config->reply_to_address = ! empty($data['reply_to_address']) ? $data['reply_to_address'] : null;
        $config->mail_enabled = (bool) ($data['mail_enabled'] ?? $this->formData['mail_enabled']);

        if (filled($data['username'] ?? null)) {
            $config->username = $data['username'];
        }
        // Only update password if a new password was provided
        if (filled($data['password'] ?? null)) {
            $config->password = $data['password'];
        }

        $config->save();
        $this->formData['id'] = $config->id;
        $this->formData['password'] = ''; // Clear secret from wire state
        $this->form->fill($this->formData);

        app(AuditWriter::class)->record(auth()->user(), 'mail.settings_saved', $config, [
            'name' => $config->name,
            'provider' => $config->provider,
            'host' => $config->host,
            'port' => $config->port,
            'encryption' => $config->encryption,
            'from_address' => $config->from_address,
            'is_active' => $config->is_active,
            'mail_enabled' => $config->mail_enabled,
        ]);

        Notification::make()
            ->title('Draft Configuration Saved')
            ->body('Settings saved to database. Run connectivity and authentication tests before activating for production.')
            ->success()
            ->send();
    }

    public function testConnection(): void
    {
        if (! auth()->user()?->can('mail_settings.update')) {
            Notification::make()->title('Unauthorized')->danger()->send();

            return;
        }

        $diagnostics = app(SmtpDiagnosticsService::class);
        $state = $this->form->getState();

        $host = $state['host'] ?? $this->formData['host'];
        $port = (int) ($state['port'] ?? $this->formData['port']);
        $encryption = $state['encryption'] ?? $this->formData['encryption'];

        $socketTest = $diagnostics->testSocketAndTls($host, $port, $encryption);

        $authTest = ['success' => false, 'error' => 'Skipped because socket connection failed.'];
        if ($socketTest['success']) {
            $username = $state['username'] ?? $this->formData['username'];
            $password = $state['password'] ?? $this->formData['password'];

            // If form password is empty, attempt to check with stored password from saved record
            if (empty($password) && ! empty($this->formData['id'])) {
                $saved = MailConfiguration::query()->find($this->formData['id']);
                $password = $saved?->password;
                if (empty($username)) {
                    $username = $saved?->username;
                }
            }

            if (! empty($username) && ! empty($password)) {
                $authTest = $diagnostics->testAuthentication($host, $port, $encryption, $username, $password);
            } else {
                $authTest = ['success' => true, 'error' => null, 'note' => 'No credentials supplied. Anonymous or unauthenticated relay.'];
            }
        }

        $this->diagnosticResult = [
            'tested_at' => now()->toDateTimeString(),
            'host' => $host,
            'port' => $port,
            'encryption' => $encryption,
            'socket' => $socketTest,
            'auth' => $authTest,
        ];

        if ($socketTest['success'] && $authTest['success']) {
            Notification::make()
                ->title('SMTP Diagnostic Passed')
                ->body("Successfully connected to {$host}:{$port} with TLS verified.")
                ->success()
                ->send();
        } else {
            $err = $socketTest['error'] ?? $authTest['error'] ?? 'Connection error';
            Notification::make()
                ->title('SMTP Diagnostic Failed')
                ->body($err)
                ->danger()
                ->send();
        }
    }

    public function sendTestEmailAction(): void
    {
        if (! auth()->user()?->can('mail_settings.update')) {
            Notification::make()->title('Unauthorized')->danger()->send();

            return;
        }

        $this->validate([
            'testRecipient' => ['required', 'email'],
        ]);

        $configId = $this->formData['id'] ?? null;
        if (! $configId) {
            $this->saveDraft();
            $configId = $this->formData['id'];
        }

        $config = MailConfiguration::query()->findOrFail($configId);

        // If the administrator entered a new password in the input field, assign it ephemerally for test
        $state = $this->form->getState();
        if (filled($state['password'] ?? null)) {
            $config->password = $state['password'];
        }

        $diagnostics = app(SmtpDiagnosticsService::class);
        $result = $diagnostics->sendTestEmail($config, $this->testRecipient, auth()->user());

        app(AuditWriter::class)->record(auth()->user(), 'mail.test_dispatched', $config, [
            'recipient' => $this->testRecipient,
            'success' => $result['success'],
            'error' => $result['error'],
        ]);

        if ($result['success']) {
            Notification::make()
                ->title('Test Email Dispatched')
                ->body("A test message was dispatched to {$this->testRecipient}.")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Test Email Delivery Failed')
                ->body($result['error'] ?? 'Could not deliver test email.')
                ->danger()
                ->send();
        }
    }

    public function activateConfiguration(): void
    {
        if (! auth()->user()?->can('mail_settings.update')) {
            Notification::make()->title('Unauthorized')->danger()->send();

            return;
        }

        $configId = $this->formData['id'] ?? null;
        if (! $configId) {
            Notification::make()->title('Save Draft First')->warning()->send();

            return;
        }

        $config = MailConfiguration::query()->findOrFail($configId);

        $config->activate();
        app(MailConfigurationService::class)->clearCache();
        app(MailConfigurationService::class)->applyRuntimeConfig($config);

        app(AuditWriter::class)->record(auth()->user(), 'mail.activated', $config, [
            'config_id' => $config->id,
            'name' => $config->name,
            'provider' => $config->provider,
        ]);

        Notification::make()
            ->title('Production Configuration Activated')
            ->body("{$config->name} is now active for all outbound application and queue emails.")
            ->success()
            ->send();
    }

    public function revertToEnv(): void
    {
        if (! auth()->user()?->can('mail_settings.update')) {
            Notification::make()->title('Unauthorized')->danger()->send();

            return;
        }

        DB::table('mail_configurations')->update(['is_active' => false]);
        app(MailConfigurationService::class)->clearCache();

        app(AuditWriter::class)->record(auth()->user(), 'mail.reverted_to_env', auth()->user(), [
            'action' => 'Deactivated all database configurations; relying on .env / config fallback.',
        ]);

        Notification::make()
            ->title('Reverted to Environment Fallback')
            ->body('All database configurations deactivated. System will use .env mail configuration.')
            ->info()
            ->send();

        $this->loadActiveOrLatestConfiguration();
        $this->form->fill($this->formData);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $activeConfig = MailConfiguration::query()->where('is_active', true)->first();
        $allConfigs = MailConfiguration::query()->latest()->get();

        return [
            'activeConfig' => $activeConfig,
            'allConfigs' => $allConfigs,
            'envFallback' => [
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
            ],
        ];
    }
}
