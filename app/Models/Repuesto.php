<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Repuesto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'cantidad',
        'cantidad_minima',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'cantidad' => 'integer',
        'cantidad_minima' => 'integer',
    ];

    public function bajoStock(): bool
    {
        return $this->cantidad <= $this->cantidad_minima;
    }
}
