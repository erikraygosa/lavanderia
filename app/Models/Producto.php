<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'precio', 'stock', 'unidad', 'activo'];

    protected $casts = ['activo' => 'boolean', 'precio' => 'decimal:2'];
}
