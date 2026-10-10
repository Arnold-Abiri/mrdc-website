<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    protected string $view = 'filament.pages.auth.login';

    protected Width|string|null $maxWidth = Width::Full;

    public function getHeading(): string|Htmlable|null
    {
        return 'Sign in to your account';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Access the Mutoko RDC administration portal';
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email Address')
            ->email()
            ->required()
            ->autocomplete('email')
            ->autofocus()
            ->prefixIcon(Heroicon::OutlinedEnvelope)
            ->placeholder('Enter your email address')
            ->extraInputAttributes([
                'class' => 'mrdc-login-input',
            ]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Password')
            ->hint(filament()->hasPasswordReset() ? new HtmlString(Blade::render('<x-filament::link :href="filament()->getRequestPasswordResetUrl()" tabindex="-1">Forgot password?</x-filament::link>')) : null)
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required()
            ->prefixIcon(Heroicon::OutlinedLockClosed)
            ->placeholder('Enter your password')
            ->extraInputAttributes([
                'class' => 'mrdc-login-input',
            ]);
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label('Sign In')
            ->submit('authenticate')
            ->icon(Heroicon::OutlinedArrowRight)
            ->iconPosition(IconPosition::After)
            ->extraAttributes([
                'class' => 'mrdc-signin-btn',
            ]);
    }
}
