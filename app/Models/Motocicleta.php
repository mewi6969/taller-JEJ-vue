<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Motocicleta extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cliente_id',
        'placa',
        'marca',
        'modelo',
        'anio',
        'cilindraje',
        'color',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function servicios(): HasMany
{
    return $this->hasMany(Servicio::class);
}
}
