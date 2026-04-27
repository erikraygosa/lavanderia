<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Corte extends Model
{
    protected $fillable = [
        'folio', 'user_id', 'fecha_inicio', 'fecha_fin',
        'total_ventas', 'total_pedidos', 'efectivo', 'tarjeta',
        'transferencia', 'otro', 'observaciones', 'cerrado_en',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'cerrado_en' => 'datetime',
        'total_ventas' => 'decimal:2',
        'efectivo' => 'decimal:2',
        'tarjeta' => 'decimal:2',
        'transferencia' => 'decimal:2',
        'otro' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public static function generarFolio(): string
    {
        $anio = now()->year;
        $ultimo = static::whereYear('created_at', $anio)->count();
        return 'CORTE-' . $anio . '-' . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
    }
}
