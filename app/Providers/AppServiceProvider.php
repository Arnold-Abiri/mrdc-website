<?php

namespace App\Providers;

use App\Domain\Identity\AuditWriter;
use App\Models\EditorialItem;
use App\Models\SiteSetting;
use App\Models\User;
use App\Policies\RolePolicy;
use Filament\Facades\Filament;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share('locale', fn (): string => app()->getLocale());
        Inertia::share('localeCsrfToken', fn (): string => csrf_token());
        Inertia::share('errors', fn (): array => session('errors') ? session('errors')->getBag('default')->messages() : []);
        Inertia::share('urgent_alerts', fn (): array => EditorialItem::query()->public()->where('type', 'notice')->where('is_urgent', true)->orderByDesc('published_at')->limit(3)->get(['slug', 'title', 'summary', 'published_at', 'category'])->toArray());
        Inertia::share('topbar_notices', fn (): array => EditorialItem::query()->public()->where('type', 'notice')->orderByDesc('published_at')->limit(5)->get(['slug', 'title', 'is_urgent'])->toArray());
        Inertia::share('site_settings', fn (): array => SiteSetting::allKeyed());
        Gate::policy(Role::class, RolePolicy::class);
        ResetPasswordNotification::createUrlUsing(fn (User $user, string $token): string => Filament::getPanel('admin')->getResetPasswordUrl($token, $user));
        ResetPasswordNotification::toMailUsing(function (User $user, string $token): MailMessage {
            $resetUrl = Filament::getPanel('admin')->getResetPasswordUrl($token, $user);

            return (new MailMessage)
                ->subject('Reset Password Notification - '.config('mail.from.name', 'Mutoko Rural District Council'))
                ->greeting('Hello!')
                ->line('You are receiving this email because we received a password reset request for your account.')
                ->action('Reset Password', $resetUrl)
                ->line('This password reset link will expire in '.config('auth.passwords.'.config('auth.defaults.passwords').'.expire').' minutes.')
                ->line('If you did not request a password reset, no further action is required.')
                ->salutation("Regards,\n".config('mail.from.name', 'Mutoko Rural District Council'));
        });
        Event::listen(Login::class, function (Login $event): void {
            if ($event->user instanceof User) {
                $event->user->forceFill(['last_login_at' => now()])->save();
                app(AuditWriter::class)->record($event->user, 'auth.login', $event->user);
            }
        });
        Event::listen(Logout::class, function (Logout $event): void {
            if ($event->user instanceof User) {
                app(AuditWriter::class)->record($event->user, 'auth.logout', $event->user);
            }
        });
        Event::listen(PasswordReset::class, function (PasswordReset $event): void {
            if ($event->user instanceof User) {
                app(AuditWriter::class)->record($event->user, 'auth.password_changed', $event->user);
            }
        });
    }
}
