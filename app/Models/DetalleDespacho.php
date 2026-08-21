<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleDespacho extends Model
{
    protected $fillable = [
        'despacho_id',
        'es_pedido',
        'numero_pedido',
        'cliente',
        'producto_id',
        'cantidad'
    ];

    public function despacho(): BelongsTo
    {
        return $this->belongsTo(Despacho::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}