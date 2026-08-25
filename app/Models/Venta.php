<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = [
        'user_id',
        'nit_ci',
        'cliente',
        'subtotal',
        'descuento',
        'total',
        'metodo_pago',
        'efectivo_recibido',
        'cambio',
        'estado',
    ];

    public function cajero()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }
}