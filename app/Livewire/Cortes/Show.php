<?php

namespace App\Livewire\Cortes;

use App\Models\Corte;
use App\Models\Pedido;
use Livewire\Component;

class Show extends Component
{
    public Corte $corte;
    public $pedidos;

    public function mount(Corte $corte): void
    {
        $this->corte = $corte->load('user');
        $this->pedidos = Pedido::with('cliente')
            ->where('estado', 'pagado')
            ->whereBetween('pagado_en', [
                $corte->fecha_inicio->startOfDay(),
                $corte->fecha_fin->endOfDay(),
            ])
            ->orderByDesc('pagado_en')
            ->get();
    }

    public function render()
    {
        return view('livewire.cortes.show')
            ->layout('layouts.app', ['title' => 'Corte ' . $this->corte->folio]);
    }
}

