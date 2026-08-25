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
    public string $fecha  = '';
    public string $desde  = '';
    public string $hasta  = '';

    protected $queryString = ['buscar', 'estado', 'fecha', 'desde', 'hasta'];

    public function updatingBuscar(): void { $this->resetPage(); }
    public function updatingEstado(): void { $this->resetPage(); }
    public function updatingFecha(): void  { $this->resetPage(); }
    public function updatingDesde(): void  { $this->resetPage(); }
    public function updatingHasta(): void  { $this->resetPage(); }

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
            ->when($this->desde, fn($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->orderByDesc('id')
            ->paginate(15);

        return view('livewire.pedidos.index', compact('pedidos'))
            ->layout('layouts.app', ['title' => 'Pedidos']);
    }
}

