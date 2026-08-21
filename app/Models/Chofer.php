<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chofer extends Model
{
    protected $table = 'choferes';

    protected $fillable = [
        'nombre',
        'activo'
    ];

    public function despachos(): HasMany
    {
        return $this->hasMany(Despacho::class);
    }
}