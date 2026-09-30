<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Domain\Identity\AuditWriter;
use App\Domain\Identity\DataScopeAuthorizer;
use Database\Factories\UserFactory;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use SensitiveParameter;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery
{
    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin' && $this->status === 'active' && app(DataScopeAuthorizer::class)->allows($this, 'admin.access', $this->department_id, $this->id);
    }

    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    use InteractsWithAppAuthentication {
        saveAppAuthenticationSecret as private persistAppAuthenticationSecret;
    }
    use InteractsWithAppAuthenticationRecovery {
        saveAppAuthenticationRecoveryCodes as private persistAppAuthenticationRecoveryCodes;
    }

    public function saveAppAuthenticationSecret(#[SensitiveParameter] ?string $secret): void
    {
        $this->persistAppAuthenticationSecret($secret);
        app(AuditWriter::class)->record($this, 'auth.mfa_secret_changed', $this);
    }

    public function saveAppAuthenticationRecoveryCodes(#[SensitiveParameter] ?array $codes): void
    {
        $this->persistAppAuthenticationRecoveryCodes($codes);
        app(AuditWriter::class)->record($this, 'auth.mfa_recovery_changed', $this);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function roleScopes(): HasMany
    {
        return $this->hasMany(UserRoleScope::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'last_login_at' => 'datetime',
            'disabled_at' => 'datetime',
        ];
    }
}
