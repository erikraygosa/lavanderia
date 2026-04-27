<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $fillable = ['nombre', 'telefono', 'email', 'direccion', 'notas', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }
}
