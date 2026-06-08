<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappFidelizacionLog extends Model
{
    protected $table = 'whatsapp_fidelizacion_log';

    protected $fillable = [
        'cliente_id',
        'telefono',
        'estado_cliente',
        'mensaje_enviado',
        'respuesta_evo',
        'enviado_at',
    ];

    protected $casts = [
        'enviado_at' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
