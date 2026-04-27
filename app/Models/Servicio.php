<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'precio', 'unidad', 'activo'];

    protected $casts = ['activo' => 'boolean', 'precio' => 'decimal:2'];
}
