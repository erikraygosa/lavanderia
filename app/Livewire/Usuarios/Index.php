<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $buscar = '';

    public function updatingBuscar(): void
    {
        $this->resetPage();
    }

    public function eliminar(int $id): void
    {
        if ($id === Auth::id()) {
            session()->flash('error', 'No puedes eliminar tu propia cuenta.');
            return;
        }
        User::findOrFail($id)->delete();
        session()->flash('exito', 'Usuario eliminado.');
    }

    public function render()
    {
        $usuarios = User::when($this->buscar, fn($q) =>
                $q->where('name', 'like', "%{$this->buscar}%")
                  ->orWhere('email', 'like', "%{$this->buscar}%")
            )
            ->latest()
            ->paginate(15);

        return view('livewire.usuarios.index', compact('usuarios'))
            ->layout('layouts.app', ['title' => 'Usuarios']);
    }
}
