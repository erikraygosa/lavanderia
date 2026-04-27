<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email    = '';
    public string $password = '';
    public bool   $remember = false;

    protected $rules = [
        'email'    => 'required|email',
        'password' => 'required|min:1',
    ];

    protected $messages = [
        'email.required'    => 'El correo es obligatorio.',
        'email.email'       => 'Ingresa un correo válido.',
        'password.required' => 'La contraseña es obligatoria.',
    ];

    public function login(): void
    {
        $this->validate();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'Correo o contraseña incorrectos.');
            return;
        }

        session()->regenerate();
        $this->redirect(route('dashboard'), navigate: false);
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.guest', ['title' => 'Iniciar sesión']);
    }
}
