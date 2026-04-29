<?php

namespace App\Livewire\Pedidos;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Producto;
use App\Models\Servicio;
use Livewire\Component;

class Form extends Component
{
    public ?Pedido $pedido = null;

    // Cliente
    public string $clienteBuscar = '';
    public ?int $clienteId = null;
    public string $clienteNombre = '';
    public array $clientesSugeridos = [];

    // Items del pedido
    public array $items = [];

    // Busqueda de items
    public string $itemBuscar = '';
    public array $itemsSugeridos = [];

    // Datos del pedido
    public string $fechaEntrega = '';
    public string $horaEntrega  = '18:00';
    public string $notas = '';
    public string $descuento = '0';
    public string $descuentoNota = '';

    public function mount(?Pedido $pedido = null): void
    {
        $this->fechaEntrega = now()->addDays(1)->format('Y-m-d');
        $this->horaEntrega  = '18:00';

        if ($pedido && $pedido->exists) {
            $this->pedido = $pedido->load('items', 'cliente');
            $this->clienteId = $pedido->cliente_id;
            $this->clienteNombre = $pedido->cliente->nombre;
            $this->fechaEntrega = $pedido->fecha_entrega?->format('Y-m-d') ?? '';
            $this->horaEntrega  = $pedido->hora_entrega ? substr($pedido->hora_entrega, 0, 5) : '18:00';
            $this->notas         = $pedido->notas ?? '';
            $this->descuento     = number_format($pedido->descuento, 2, '.', '');
            $this->descuentoNota = $pedido->descuento_nota ?? '';

            foreach ($pedido->items as $item) {
                $this->items[] = [
                    'tipo'           => $item->tipo,
                    'item_id'        => $item->item_id,
                    'descripcion'    => $item->descripcion,
                    'cantidad'       => (float) $item->cantidad,
                    'precio_unitario'=> (float) $item->precio_unitario,
                    'subtotal'       => (float) $item->subtotal,
                ];
            }
        }
    }

    public function buscarCliente(): void
    {
        if (strlen($this->clienteBuscar) < 2) {
            $this->clientesSugeridos = [];
            return;
        }

        $this->clientesSugeridos = Cliente::where('activo', true)
            ->where(fn($q) => $q->where('nombre', 'like', "%{$this->clienteBuscar}%")
                ->orWhere('telefono', 'like', "%{$this->clienteBuscar}%"))
            ->limit(6)
            ->get(['id', 'nombre', 'telefono'])
            ->toArray();
    }

    public function seleccionarCliente(int $id, string $nombre): void
    {
        $this->clienteId = $id;
        $this->clienteNombre = $nombre;
        $this->clienteBuscar = '';
        $this->clientesSugeridos = [];
    }

    public function limpiarCliente(): void
    {
        $this->clienteId = null;
        $this->clienteNombre = '';
    }

    public function buscarItem(): void
    {
        if (strlen($this->itemBuscar) < 2) {
            $this->itemsSugeridos = [];
            return;
        }

        $servicios = Servicio::where('activo', true)
            ->where('nombre', 'like', "%{$this->itemBuscar}%")
            ->limit(5)->get()
            ->map(fn($s) => ['tipo' => 'servicio', 'id' => $s->id, 'nombre' => $s->nombre, 'precio' => (float)$s->precio, 'unidad' => $s->unidad]);

        $productos = Producto::where('activo', true)
            ->where('nombre', 'like', "%{$this->itemBuscar}%")
            ->limit(5)->get()
            ->map(fn($p) => ['tipo' => 'producto', 'id' => $p->id, 'nombre' => $p->nombre, 'precio' => (float)$p->precio, 'unidad' => $p->unidad]);

        $this->itemsSugeridos = $servicios->concat($productos)->toArray();
    }

    public function agregarItem(int $itemId, string $tipo, string $nombre, float $precio): void
    {
        $this->items[] = [
            'tipo'            => $tipo,
            'item_id'         => $itemId,
            'descripcion'     => $nombre,
            'cantidad'        => 1,
            'precio_unitario' => $precio,
            'subtotal'        => $precio,
        ];

        $this->itemBuscar = '';
        $this->itemsSugeridos = [];
    }

    public function actualizarCantidad(int $index, float $cantidad): void
    {
        if ($cantidad <= 0) {
            $this->quitarItem($index);
            return;
        }
        $this->items[$index]['cantidad'] = $cantidad;
        $this->recalcularSubtotal($index);
    }

    public function actualizarPrecio(int $index, float $precio): void
    {
        $this->items[$index]['precio_unitario'] = $precio;
        $this->recalcularSubtotal($index);
    }

    private function recalcularSubtotal(int $index): void
    {
        $this->items[$index]['subtotal'] = round(
            $this->items[$index]['cantidad'] * $this->items[$index]['precio_unitario'],
            2
        );
    }

    public function quitarItem(int $index): void
    {
        array_splice($this->items, $index, 1);
        $this->items = array_values($this->items);
    }

    public function getSubtotalProperty(): float
    {
        return round(collect($this->items)->sum('subtotal'), 2);
    }

    public function getTotalProperty(): float
    {
        return round(max(0, $this->subtotal - (float)($this->descuento ?? 0)), 2);
    }

    public function guardar(): void
    {
        $this->validate([
            'clienteId'    => 'required|exists:clientes,id',
            'fechaEntrega' => 'required|date',
            'items'        => 'required|array|min:1',
            'descuento'    => 'nullable|numeric|min:0',
        ], [
            'clienteId.required'  => 'Selecciona un cliente.',
            'fechaEntrega.required'=> 'La fecha de entrega es obligatoria.',
            'items.min'           => 'Agrega al menos un servicio o producto.',
        ]);

        $datosBase = [
            'cliente_id'    => $this->clienteId,
            'fecha_entrega' => $this->fechaEntrega,
            'hora_entrega'  => $this->horaEntrega ?: null,
            'notas'          => $this->notas,
            'subtotal'       => $this->subtotal,
            'descuento'      => (float)($this->descuento ?? 0),
            'descuento_nota' => $this->descuentoNota ?: null,
            'total'          => $this->total,
        ];

        if ($this->pedido && $this->pedido->exists) {
            $this->pedido->update($datosBase);
            $this->pedido->items()->delete();
            $pedido = $this->pedido;
        } else {
            $datosBase['folio'] = Pedido::generarFolio();
            $datosBase['estado'] = 'pendiente';
            $pedido = Pedido::create($datosBase);
        }

        foreach ($this->items as $item) {
            PedidoItem::create([
                'pedido_id'       => $pedido->id,
                'tipo'            => $item['tipo'],
                'item_id'         => $item['item_id'],
                'descripcion'     => $item['descripcion'],
                'cantidad'        => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'subtotal'        => $item['subtotal'],
            ]);
        }

        session()->flash('exito', $this->pedido?->exists ? 'Pedido actualizado.' : 'Pedido creado correctamente.');
        $this->redirect(route('pedidos.show', $pedido));
    }

    public function render()
    {
        $titulo = $this->pedido?->exists ? 'Editar pedido' : 'Nuevo pedido';
        return view('livewire.pedidos.form')
            ->layout('layouts.app', ['title' => $titulo]);
    }
}

