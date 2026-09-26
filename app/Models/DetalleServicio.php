<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleServicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'servicio_id',
        'repuesto_id',
        'cantidad',
        'precio_unitario',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::created(function (DetalleServicio $detalle) {
            $detalle->repuesto()->decrement('cantidad', $detalle->cantidad);
            $detalle->servicio->recalcularCostoTotal();
        });

        static::deleted(function (DetalleServicio $detalle) {
            $detalle->repuesto()->increment('cantidad', $detalle->cantidad);
            $detalle->servicio->recalcularCostoTotal();
        });
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }

    public function repuesto(): BelongsTo
    {
        return $this->belongsTo(Repuesto::class);
    }
}
