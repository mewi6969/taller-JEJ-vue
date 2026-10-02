<?php

namespace App\Models;

use App\Mail\ServicioTerminadoMail;
use Database\Factories\ServicioFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class Servicio extends Model
{
    /** @use HasFactory<ServicioFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'motocicleta_id',
        'mecanico_id',
        'descripcion_problema',
        'estado',
        'costo_mano_obra',
        'costo_total',
        'fecha_ingreso',
        'fecha_entrega',
        'observaciones',
    ];

    protected $casts = [
        'costo_mano_obra' => 'decimal:2',
        'costo_total' => 'decimal:2',
        'fecha_ingreso' => 'date',
        'fecha_entrega' => 'date',
    ];

    protected static function booted(): void
    {
        static::updated(function (Servicio $servicio) {
            if ($servicio->wasChanged('estado') && $servicio->estado === 'terminado') {
                $cliente = $servicio->motocicleta->cliente;

                if ($cliente && $cliente->email) {
                    Mail::to($cliente->email)->send(new ServicioTerminadoMail($servicio));
                }
            }
        });
    }

    /**
     * @return BelongsTo<Motocicleta, $this>
     */
    public function motocicleta(): BelongsTo
    {
        return $this->belongsTo(Motocicleta::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function mecanico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }

    /**
     * @return HasMany<DetalleServicio, $this>
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleServicio::class);
    }

    /**
     * @return HasOne<Factura, $this>
     */
    public function factura(): HasOne
    {
        return $this->hasOne(Factura::class);
    }

    public function recalcularCostoTotal(): void
    {
        $costoRepuestos = $this->detalles()->sum(
            DB::raw('cantidad * precio_unitario')
        );

        $this->update([
            'costo_total' => $this->costo_mano_obra + $costoRepuestos,
        ]);
    }
}
