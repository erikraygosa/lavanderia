<?php

namespace App\Livewire\Cortes;

use App\Models\Corte;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $cortes = Corte::with('user')
            ->orderByDesc('id')
            ->paginate(15);

        return view('livewire.cortes.index', compact('cortes'))
            ->layout('layouts.app', ['title' => 'Cortes de caja']);
    }
}

