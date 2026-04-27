<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $buscar = '';
    public string $confirmandoEliminar = '';

    protected $queryString = ['buscar'];

    public function updatingBuscar(): void
    {
        $this->resetPage();
    }

    public function eliminar(int $id): void
    {
        $cliente = Cliente::findOrFail($id);
        if ($cliente->pedidos()->exists()) {
            session()->flash('error', 'No se puede eliminar: el cliente tiene pedidos asociados.');
            return;
        }
        $cliente->delete();
        session()->flash('exito', 'Cliente eliminado correctamente.');
        $this->confirmandoEliminar = '';
    }

    public function render()
    {
        $clientes = Cliente::query()
            ->when($this->buscar, fn($q) => $q->where('nombre', 'like', "%{$this->buscar}%")
                ->orWhere('telefono', 'like', "%{$this->buscar}%")
                ->orWhere('email', 'like', "%{$this->buscar}%"))
            ->orderBy('nombre')
            ->paginate(15);

        return view('livewire.clientes.index', compact('clientes'))
            ->layout('layouts.app', ['title' => 'Clientes']);
    }
}

