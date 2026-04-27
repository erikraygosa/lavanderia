<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Livewire\Component;

class Form extends Component
{
    public ?Cliente $cliente = null;

    public string $nombre = '';
    public string $telefono = '';
    public string $email = '';
    public string $direccion = '';
    public string $notas = '';

    public bool $activo = true;

    protected function rules(): array
    {
        return [
            'nombre'    => 'required|min:2|max:100',
            'telefono'  => 'nullable|max:20',
            'email'     => 'nullable|email|max:100',
            'direccion' => 'nullable|max:200',
            'notas'     => 'nullable|max:500',
            'activo'    => 'boolean',
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'nombre.min'      => 'El nombre debe tener al menos 2 caracteres.',
        'email.email'     => 'El correo no tiene formato válido.',
    ];

    public function mount(?Cliente $cliente = null): void
    {
        if ($cliente && $cliente->exists) {
            $this->cliente   = $cliente;
            $this->nombre    = $cliente->nombre ?? '';
            $this->telefono  = $cliente->telefono ?? '';
            $this->email     = $cliente->email ?? '';
            $this->direccion = $cliente->direccion ?? '';
            $this->notas     = $cliente->notas ?? '';
            $this->activo    = (bool) $cliente->activo;
        }
    }

    public function guardar(): void
    {
        $this->validate();

        $datos = [
            'nombre'    => $this->nombre,
            'telefono'  => $this->telefono,
            'email'     => $this->email ?: null,
            'direccion' => $this->direccion,
            'notas'     => $this->notas,
            'activo'    => $this->activo,
        ];

        if ($this->cliente && $this->cliente->exists) {
            $this->cliente->update($datos);
            session()->flash('exito', 'Cliente actualizado correctamente.');
        } else {
            Cliente::create($datos);
            session()->flash('exito', 'Cliente creado correctamente.');
        }

        $this->redirect(route('clientes.index'));
    }

    public function render()
    {
        $titulo = $this->cliente?->exists ? 'Editar cliente' : 'Nuevo cliente';
        return view('livewire.clientes.form')
            ->layout('layouts.app', ['title' => $titulo]);
    }
}

