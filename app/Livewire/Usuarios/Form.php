<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    public ?User $usuario = null;

    public string $name     = '';
    public string $email    = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $rol      = 'operador';
    public array  $permisos = [];

    public function mount(?User $usuario = null): void
    {
        if ($usuario && $usuario->exists) {
            $this->usuario  = $usuario;
            $this->name     = $usuario->name;
            $this->email    = $usuario->email;
            $this->rol      = $usuario->rol;
            $this->permisos = $usuario->permisos ?? [];
        }
    }

    protected function rules(): array
    {
        $userId = $this->usuario?->id;

        return [
            'name'     => 'required|min:2|max:100',
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'password' => $userId ? 'nullable|min:6|confirmed' : 'required|min:6|confirmed',
            'rol'      => 'required|in:admin,operador',
            'permisos' => 'array',
            'permisos.*' => 'string|in:' . implode(',', array_keys(User::permisosDisponibles())),
        ];
    }

    protected $messages = [
        'name.required'      => 'El nombre es obligatorio.',
        'email.required'     => 'El correo es obligatorio.',
        'email.email'        => 'Ingresa un correo válido.',
        'email.unique'       => 'Este correo ya está en uso.',
        'password.required'  => 'La contraseña es obligatoria.',
        'password.min'       => 'Mínimo 6 caracteres.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
    ];

    public function updatedRol(): void
    {
        // Al cambiar a admin, limpiar permisos (no son necesarios)
        if ($this->rol === 'admin') {
            $this->permisos = [];
        }
    }

    public function guardar(): void
    {
        $this->validate();

        $datos = [
            'name'     => $this->name,
            'email'    => $this->email,
            'rol'      => $this->rol,
            'permisos' => $this->rol === 'admin' ? null : ($this->permisos ?: null),
        ];

        if ($this->password) {
            $datos['password'] = Hash::make($this->password);
        }

        if ($this->usuario && $this->usuario->exists) {
            $this->usuario->update($datos);
            session()->flash('exito', 'Usuario actualizado.');
        } else {
            User::create($datos);
            session()->flash('exito', 'Usuario creado correctamente.');
        }

        $this->redirect(route('usuarios.index'));
    }

    public function render()
    {
        $titulo = $this->usuario?->exists ? 'Editar usuario' : 'Nuevo usuario';
        return view('livewire.usuarios.form', [
            'permisosDisponibles' => User::permisosDisponibles(),
        ])->layout('layouts.app', ['title' => $titulo]);
    }
}
