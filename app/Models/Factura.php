<?php

namespace App\Models;

use Database\Factories\FacturaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factura extends Model
{
    /** @use HasFactory<FacturaFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero_factura',
        'servicio_id',
        'subtotal',
        'descuento',
        'total',
        'estado',
        'metodo_pago',
        'fecha_pago',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha_pago' => 'date',
    ];

    protected static function booted(): void
    {
        static::created(function (Factura $factura) {
            $factura->updateQuietly([
                'numero_factura' => 'FAC-'.str_pad((string) $factura->id, 6, '0', STR_PAD_LEFT),
            ]);
        });
    }

    /**
     * @return BelongsTo<Servicio, $this>
     */
    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }
}
