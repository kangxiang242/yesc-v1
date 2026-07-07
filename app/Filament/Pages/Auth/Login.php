<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getLoginFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data')
            ->extraAttributes(['autocomplete' => 'off']);
    }

    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('login')
            ->label('帳號')
            ->placeholder('请输入用户名或邮箱')
            ->required()
            ->autocomplete(false)
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1, 'autocomplete' => 'off']);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('密碼')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete(false)
            ->required()
            ->extraInputAttributes(['tabindex' => 2, 'autocomplete' => 'new-password']);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login = $data['login'] ?? '';

        // 判断输入的是邮箱还是用户名
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return [
                'email'    => $login,
                'password' => $data['password'] ?? '',
            ];
        }

        return [
            'name'     => $login,
            'password' => $data['password'] ?? '',
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

    public function getHeading(): string | Htmlable
    {
        return config('app.name') . ' 後台登入';
    }
}
