<?php

namespace App\Livewire\Servicios;

use App\Models\Servicio;
use Livewire\Component;

class Form extends Component
{
    public ?Servicio $servicio = null;

    public string $nombre = '';
    public string $descripcion = '';
    public string $precio = '';
    public string $unidad = 'pieza';
    public bool $activo = true;

    public array $unidades = ['pieza', 'kg', 'lote', 'docena', 'par', 'metro'];

    protected function rules(): array
    {
        return [
            'nombre'      => 'required|min:2|max:100',
            'descripcion' => 'nullable|max:300',
            'precio'      => 'required|numeric|min:0',
            'unidad'      => 'required',
            'activo'      => 'boolean',
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'precio.required' => 'El precio es obligatorio.',
        'precio.numeric'  => 'El precio debe ser un número.',
    ];

    public function mount(?Servicio $servicio = null): void
    {
        if ($servicio && $servicio->exists) {
            $this->servicio = $servicio;
            $this->fill($servicio->only('nombre', 'descripcion', 'unidad', 'activo'));
            $this->precio = number_format($servicio->precio, 2, '.', '');
        }
    }

    public function guardar(): void
    {
        $this->validate();
        $datos = [
            'nombre'      => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio'      => $this->precio,
            'unidad'      => $this->unidad,
            'activo'      => $this->activo,
        ];

        if ($this->servicio && $this->servicio->exists) {
            $this->servicio->update($datos);
            session()->flash('exito', 'Servicio actualizado.');
        } else {
            Servicio::create($datos);
            session()->flash('exito', 'Servicio creado.');
        }

        $this->redirect(route('servicios.index'));
    }

    public function render()
    {
        $titulo = $this->servicio?->exists ? 'Editar servicio' : 'Nuevo servicio';
        return view('livewire.servicios.form')
            ->layout('layouts.app', ['title' => $titulo]);
    }
}

