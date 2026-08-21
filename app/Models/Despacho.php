<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Despacho extends Model
{
    protected $fillable = [
        'chofer_id',
        'fecha',
        'estado',
        'observacion',
        'confirmado_en'
    ];

    public function chofer(): BelongsTo
    {
        return $this->belongsTo(Chofer::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleDespacho::class);
    }
}