<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'categoria_id',
        'activo'
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function inventario(): HasMany
    {
        return $this->hasMany(Inventario::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }
}