<?php

namespace App\Services;

use App\Models\Cliente;
use Carbon\Carbon;

class FidelizacionService
{
    /**
     * Clasifica un cliente y retorna todos sus datos de fidelización.
     * Acepta un modelo Cliente ya cargado (con pedidos e items eager-loaded)
     * para evitar N+1 cuando se clasifica en lote.
     */
    public function clasificarCliente(Cliente $cliente): array
    {
        // Reutiliza la relación si ya está cargada; de lo contrario la consulta
        $pedidos = $cliente->relationLoaded('pedidos')
            ? $cliente->pedidos
            : $cliente->pedidos()->where('estado', '!=', 'abandonado')->with('items')->get();

        $totalPedidos  = $pedidos->count();
        $ultimoPedido  = $pedidos->sortByDesc('created_at')->first();
        $ahora         = Carbon::now('America/Merida');

        $diasSinVenir = $ultimoPedido
            ? (int) $ahora->diffInDays($ultimoPedido->created_at)
            : 9999; // centinela para ordenar sin-visita al inicio del DESC

        $ticketPromedio = $totalPedidos > 0
            ? (float) round($pedidos->avg('total'), 2)
            : 0.0;

        // Servicios más frecuentes (top 3 por nombre de descripción)
        $serviciosFrecuentes = $pedidos
            ->flatMap(fn($p) => $p->relationLoaded('items') ? $p->items : $p->items()->get())
            ->where('tipo', 'servicio')
            ->groupBy('descripcion')
            ->map->count()
            ->sortDesc()
            ->take(3)
            ->keys()
            ->toArray();

        [$estado, $color] = $this->determinarEstado($totalPedidos, $diasSinVenir);

        return [
            'cliente'             => $cliente,
            'estado'              => $estado,
            'color'               => $color,
            'total_pedidos'       => $totalPedidos,
            'ultimo_pedido'       => $ultimoPedido?->created_at,
            'dias_sin_venir'      => $diasSinVenir,
            'ticket_promedio'     => $ticketPromedio,
            'servicios_frecuentes'=> $serviciosFrecuentes,
            'mensaje_sugerido'    => $this->mensajeSugerido($cliente->nombre, $estado),
        ];
    }

    /**
     * Aplica las reglas de clasificación y retorna [estado, color].
     *
     * | Estado    | Criterio                                                     |
     * |-----------|--------------------------------------------------------------|
     * | NUEVO     | 1 pedido y fue hace menos de 7 días                          |
     * | ACTIVO    | 2+ pedidos y el último fue hace menos de 21 días             |
     * | EN_RIESGO | 2+ pedidos y el último fue hace entre 21 y 40 días           |
     * | INACTIVO  | Sin pedidos, 1 pedido hace 7+ días, o último pedido 40+ días |
     */
    private function determinarEstado(int $totalPedidos, int $diasSinVenir): array
    {
        if ($totalPedidos === 1 && $diasSinVenir < 7) {
            return ['NUEVO', 'blue'];
        }

        if ($totalPedidos >= 2 && $diasSinVenir < 21) {
            return ['ACTIVO', 'green'];
        }

        if ($totalPedidos >= 2 && $diasSinVenir >= 21 && $diasSinVenir <= 40) {
            return ['EN_RIESGO', 'amber'];
        }

        return ['INACTIVO', 'red'];
    }

    /**
     * Retorna el mensaje de fidelización pre-llenado con el nombre del cliente.
     */
    public function mensajeSugerido(string $nombre, string $estado): string
    {
        return match ($estado) {
            'NUEVO' =>
                "¡Hola {$nombre}! 👋 Gracias por confiar en Lavandería y Tintorería Laundry Lizette 💜\n" .
                "Esperamos que tu ropa haya quedado tal como la esperabas.\n" .
                "En tu próxima visita tienes un 10% de descuento especial 🎉\n" .
                "¡Te esperamos pronto! 🐾\n" .
                "— Laundry Lizette | 999 262 0820",

            'ACTIVO' =>
                "¡Hola {$nombre}! 🌟 Gracias por seguir eligiendo a Lavandería y Tintorería Laundry Lizette.\n" .
                "Eres parte de nuestra familia especial 💜\n" .
                "En tu próxima visita tienes un servicio de planchado gratis 🎁\n" .
                "¡Gracias por tu confianza! 🐾\n" .
                "— Laundry Lizette | 999 262 0820",

            'EN_RIESGO' =>
                "¡Hola {$nombre}! 💜 Ya te extrañamos en Laundry Lizette 🐾\n" .
                "¿Tienes ropa acumulada o alguna prenda especial que cuidar?\n" .
                "Esta semana tenemos servicio express disponible.\n" .
                "¡Escríbenos y con gusto te atendemos! 😊\n" .
                "— Laundry Lizette | 999 262 0820",

            default =>
                "¡Hola {$nombre}! 😊 Hace tiempo que no sabemos de ti en Lavandería y Tintorería Laundry Lizette.\n" .
                "Queremos que sepas que siempre habrá un lugar para cuidar tu ropa con todo el cariño 💜\n" .
                "Esta semana tenemos una oferta especial para clientes que regresan 🎉\n" .
                "— Laundry Lizette | 999 262 0820",
        };
    }
}
