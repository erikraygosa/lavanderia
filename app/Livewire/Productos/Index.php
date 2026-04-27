<?php

namespace App\Livewire\Productos;

use App\Models\Producto;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $buscar = '';

    protected $queryString = ['buscar'];

    public function updatingBuscar(): void
    {
        $this->resetPage();
    }

    public function toggleActivo(int $id): void
    {
        $producto = Producto::findOrFail($id);
        $producto->update(['activo' => !$producto->activo]);
    }

    public function eliminar(int $id): void
    {
        Producto::findOrFail($id)->delete();
        session()->flash('exito', 'Producto eliminado.');
    }

    public function render()
    {
        $productos = Producto::query()
            ->when($this->buscar, fn($q) => $q->where('nombre', 'like', "%{$this->buscar}%"))
            ->orderBy('nombre')
            ->paginate(15);

        return view('livewire.productos.index', compact('productos'))
            ->layout('layouts.app', ['title' => 'Productos']);
    }
}

