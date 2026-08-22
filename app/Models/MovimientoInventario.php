<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'producto_id',
        'tipo',
        'cantidad',
        'referencia',
        'observacion',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];
}
