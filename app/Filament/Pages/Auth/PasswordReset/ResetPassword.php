<?php

namespace App\Filament\Pages\Auth\PasswordReset;

use Filament\Actions\Action;
use Filament\Auth\Pages\PasswordReset\ResetPassword as BaseResetPassword;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ResetPassword extends BaseResetPassword
{
    protected string $view = 'filament.pages.auth.password-reset.reset';

    protected Width|string|null $maxWidth = Width::Full;

    public function getHeading(): string|Htmlable|null
    {
        return 'Choose a new password';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Please choose a strong password for your administrator account.';
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email Address')
            ->disabled()
            ->prefixIcon(Heroicon::OutlinedEnvelope)
            ->extraInputAttributes([
                'class' => 'mrdc-login-input',
            ]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('New Password')
            ->password()
            ->autocomplete('new-password')
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->rule(PasswordRule::default())
            ->same('passwordConfirmation')
            ->validationAttribute('password')
            ->prefixIcon(Heroicon::OutlinedLockClosed)
            ->placeholder('Enter your new password')
            ->extraInputAttributes([
                'class' => 'mrdc-login-input',
            ]);
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label('Confirm Password')
            ->password()
            ->autocomplete('new-password')
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->dehydrated(false)
            ->prefixIcon(Heroicon::OutlinedLockClosed)
            ->placeholder('Re-enter your new password')
            ->extraInputAttributes([
                'class' => 'mrdc-login-input',
            ]);
    }

    public function getResetPasswordFormAction(): Action
    {
        return Action::make('resetPassword')
            ->label('Reset Password')
            ->submit('resetPassword')
            ->icon(Heroicon::OutlinedArrowRight)
            ->iconPosition(IconPosition::After)
            ->extraAttributes([
                'class' => 'mrdc-signin-btn',
            ]);
    }
}
