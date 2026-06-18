<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoPago extends Model
{
    protected $fillable = ['pedido_id', 'monto', 'metodo_pago', 'tipo'];

    protected $casts = ['monto' => 'decimal:2'];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
