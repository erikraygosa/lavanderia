<?php

namespace App\Livewire\Productos;

use App\Models\Producto;
use Livewire\Component;

class Form extends Component
{
    public ?Producto $producto = null;

    public string $nombre = '';
    public string $descripcion = '';
    public string $precio = '';
    public int $stock = 0;
    public string $unidad = 'pieza';
    public bool $activo = true;

    public array $unidades = ['pieza', 'kg', 'litro', 'caja', 'bolsa', 'rollo'];

    protected function rules(): array
    {
        return [
            'nombre'      => 'required|min:2|max:100',
            'descripcion' => 'nullable|max:300',
            'precio'      => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'unidad'      => 'required',
            'activo'      => 'boolean',
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'precio.required' => 'El precio es obligatorio.',
        'precio.numeric'  => 'El precio debe ser un número.',
    ];

    public function mount(?Producto $producto = null): void
    {
        if ($producto && $producto->exists) {
            $this->producto = $producto;
            $this->fill($producto->only('nombre', 'descripcion', 'stock', 'unidad', 'activo'));
            $this->precio = number_format($producto->precio, 2, '.', '');
        }
    }

    public function guardar(): void
    {
        $this->validate();
        $datos = [
            'nombre'      => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio'      => $this->precio,
            'stock'       => $this->stock,
            'unidad'      => $this->unidad,
            'activo'      => $this->activo,
        ];

        if ($this->producto && $this->producto->exists) {
            $this->producto->update($datos);
            session()->flash('exito', 'Producto actualizado.');
        } else {
            Producto::create($datos);
            session()->flash('exito', 'Producto creado.');
        }

        $this->redirect(route('productos.index'));
    }

    public function render()
    {
        $titulo = $this->producto?->exists ? 'Editar producto' : 'Nuevo producto';
        return view('livewire.productos.form')
            ->layout('layouts.app', ['title' => $titulo]);
    }
}

