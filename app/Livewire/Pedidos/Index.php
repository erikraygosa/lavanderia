<?php

namespace App\Livewire\Pedidos;

use App\Models\Pedido;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $buscar = '';
    public string $estado = '';
    public string $fecha = '';

    protected $queryString = ['buscar', 'estado', 'fecha'];

    public function updatingBuscar(): void { $this->resetPage(); }
    public function updatingEstado(): void { $this->resetPage(); }
    public function updatingFecha(): void  { $this->resetPage(); }

    public function cambiarEstado(int $id, string $estado): void
    {
        $pedido = Pedido::findOrFail($id);
        $datos = ['estado' => $estado];

        if ($estado === 'pagado' && !$pedido->pagado_en) {
            $datos['pagado_en'] = now();
        }

        $pedido->update($datos);
    }

    public function render()
    {
        $pedidos = Pedido::with('cliente')
            ->when($this->buscar, fn($q) => $q->where('folio', 'like', "%{$this->buscar}%")
                ->orWhereHas('cliente', fn($c) => $c->where('nombre', 'like', "%{$this->buscar}%")))
            ->when($this->estado, fn($q) => $q->where('estado', $this->estado))
            ->when($this->fecha, fn($q) => $q->whereDate('created_at', $this->fecha))
            ->orderByDesc('id')
            ->paginate(15);

        return view('livewire.pedidos.index', compact('pedidos'))
            ->layout('layouts.app', ['title' => 'Pedidos']);
    }
}

