<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    protected $fillable = [
        'folio', 'cliente_id', 'estado',
        'fecha_entrega', 'hora_entrega',
        'es_domicilio', 'direccion_domicilio',
        'subtotal', 'descuento', 'descuento_nota', 'total',
        'metodo_pago', 'notas',
        'pagado_en', 'terminado_en', 'entregado_en',
    ];

    protected $casts = [
        'fecha_entrega'  => 'date',
        'pagado_en'      => 'datetime',
        'terminado_en'   => 'datetime',
        'entregado_en'   => 'datetime',
        'subtotal'       => 'decimal:2',
        'descuento'      => 'decimal:2',
        'total'          => 'decimal:2',
        'es_domicilio'   => 'boolean',
    ];

    public function entregaFormateada(): string
    {
        if (!$this->fecha_entrega) return 'No definida';
        $fecha = $this->fecha_entrega->format('d/m/Y');
        $hora  = $this->hora_entrega ? ' ' . substr($this->hora_entrega, 0, 5) . ' hrs' : '';
        return $fecha . $hora;
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }

    public static function generarFolio(): string
    {
        $anio   = now()->year;
        $ultimo = static::whereYear('created_at', $anio)->count();
        return 'LAV-' . $anio . '-' . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
    }

    public function estadoBadge(): array
    {
        return match($this->estado) {
            'pendiente'  => ['color' => 'yellow', 'texto' => 'Pendiente',  'icon' => '⏳'],
            'terminado'  => ['color' => 'blue',   'texto' => 'Listo',      'icon' => '✅'],
            'entregado'  => ['color' => 'purple', 'texto' => 'Entregado',  'icon' => '📦'],
            'pagado'     => ['color' => 'green',  'texto' => 'Pagado',     'icon' => '💵'],
            'abandonado' => ['color' => 'red',    'texto' => 'Abandonado', 'icon' => '❌'],
            default      => ['color' => 'gray',   'texto' => $this->estado, 'icon' => ''],
        };
    }
}
