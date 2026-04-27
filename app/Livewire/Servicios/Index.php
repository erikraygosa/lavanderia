<?php

namespace App\Livewire\Servicios;

use App\Models\Servicio;
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
        $servicio = Servicio::findOrFail($id);
        $servicio->update(['activo' => !$servicio->activo]);
    }

    public function eliminar(int $id): void
    {
        Servicio::findOrFail($id)->delete();
        session()->flash('exito', 'Servicio eliminado.');
    }

    public function render()
    {
        $servicios = Servicio::query()
            ->when($this->buscar, fn($q) => $q->where('nombre', 'like', "%{$this->buscar}%"))
            ->orderBy('nombre')
            ->paginate(15);

        return view('livewire.servicios.index', compact('servicios'))
            ->layout('layouts.app', ['title' => 'Servicios']);
    }
}

