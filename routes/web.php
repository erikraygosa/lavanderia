<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ── Ticket público con token global (sin login — para imprimir desde terminal)
Route::get('/ticket/{id}', function ($id) {
    abort_unless(request('token') === config('app.ticket_token'), 403, 'Token inválido.');
    $pedido = \App\Models\Pedido::with('items', 'cliente')->findOrFail($id);
    return view('pedidos.ticket', compact('pedido'));
})->name('pedidos.ticket.publico');

// ── Autenticación ───────────────────────────────────────────────────────────
Route::get('/login', \App\Livewire\Auth\Login::class)->name('login')->middleware('guest');
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');

// ── Rutas protegidas ────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        $destino = auth()->user()->tienePermiso('dashboard')
            ? route('dashboard')
            : route('pedidos.index');
        return redirect($destino);
    });

    // Dashboard — sin middleware en ruta; el componente redirige si no hay permiso
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');

    // Clientes — cualquier usuario autenticado puede ver/gestionar
    Route::prefix('clientes')->name('clientes.')->group(function () {
        Route::get('/', \App\Livewire\Clientes\Index::class)->name('index');
        Route::get('/crear', \App\Livewire\Clientes\Form::class)->name('crear');
        Route::get('/{cliente}/editar', \App\Livewire\Clientes\Form::class)->name('editar');
    });

    // Servicios — editar requiere permiso de precios
    Route::prefix('servicios')->name('servicios.')->group(function () {
        Route::get('/', \App\Livewire\Servicios\Index::class)->name('index');
        Route::get('/crear', \App\Livewire\Servicios\Form::class)
            ->name('crear')->middleware('permiso:precios.editar');
        Route::get('/{servicio}/editar', \App\Livewire\Servicios\Form::class)
            ->name('editar')->middleware('permiso:precios.editar');
    });

    // Productos — editar requiere permiso de precios
    Route::prefix('productos')->name('productos.')->group(function () {
        Route::get('/', \App\Livewire\Productos\Index::class)->name('index');
        Route::get('/crear', \App\Livewire\Productos\Form::class)
            ->name('crear')->middleware('permiso:precios.editar');
        Route::get('/{producto}/editar', \App\Livewire\Productos\Form::class)
            ->name('editar')->middleware('permiso:precios.editar');
    });

    // Pedidos
    Route::prefix('pedidos')->name('pedidos.')->group(function () {
        Route::get('/', \App\Livewire\Pedidos\Index::class)->name('index');
        Route::get('/crear', \App\Livewire\Pedidos\Form::class)
            ->name('crear')->middleware('permiso:pedidos.editar');
        Route::get('/{pedido}/editar', \App\Livewire\Pedidos\Form::class)
            ->name('editar')->middleware('permiso:pedidos.editar');
        Route::get('/{pedido}/ticket', function (\App\Models\Pedido $pedido) {
            return view('pedidos.ticket', compact('pedido'));
        })->name('ticket');
        Route::get('/{pedido}', \App\Livewire\Pedidos\Show::class)->name('show');
    });

    // Cortes de caja
    Route::prefix('cortes')->name('cortes.')->middleware('permiso:cortes')->group(function () {
        Route::get('/', \App\Livewire\Cortes\Index::class)->name('index');
        Route::get('/crear', \App\Livewire\Cortes\Form::class)->name('crear');
        Route::get('/{corte}', \App\Livewire\Cortes\Show::class)->name('show');
    });

    // Usuarios
    Route::prefix('usuarios')->name('usuarios.')->middleware('permiso:usuarios')->group(function () {
        Route::get('/', \App\Livewire\Usuarios\Index::class)->name('index');
        Route::get('/crear', \App\Livewire\Usuarios\Form::class)->name('crear');
        Route::get('/{usuario}/editar', \App\Livewire\Usuarios\Form::class)->name('editar');
    });

    // Fidelización de clientes
    Route::get('/fidelizacion', \App\Livewire\Fidelizacion\ClientesFidelizacion::class)
        ->name('fidelizacion.index');
    Route::get('/fidelizacion/{cliente}/mensaje', \App\Livewire\Fidelizacion\MensajeCliente::class)
        ->name('fidelizacion.mensaje');

    // Configuración
    Route::get('/configuracion', \App\Livewire\Configuracion\Index::class)
        ->name('configuracion.index')
        ->middleware('permiso:configuracion');
});
