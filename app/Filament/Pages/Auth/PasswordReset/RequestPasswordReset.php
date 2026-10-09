<?php

namespace App\Filament\Pages\Auth\PasswordReset;

use Filament\Actions\Action;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    protected string $view = 'filament.pages.auth.password-reset.request';

    protected Width|string|null $maxWidth = Width::Full;

    public function getHeading(): string|Htmlable|null
    {
        return 'Reset your password';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Enter your email address and we will send you a password reset link.';
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
            ->placeholder('Enter your registered email address')
            ->extraInputAttributes([
                'class' => 'mrdc-login-input',
            ]);
    }

    protected function getRequestFormAction(): Action
    {
        return Action::make('request')
            ->label('Send Password Reset Link')
            ->submit('request')
            ->icon(Heroicon::OutlinedArrowRight)
            ->iconPosition(IconPosition::After)
            ->extraAttributes([
                'class' => 'mrdc-signin-btn',
            ]);
    }
}
