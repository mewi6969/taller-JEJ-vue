<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio extends Model
{
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

    public function motocicleta(): BelongsTo
    {
        return $this->belongsTo(Motocicleta::class);
    }

    public function mecanico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleServicio::class);
    }

    public function recalcularCostoTotal(): void
    {
        $costoRepuestos = $this->detalles()->sum(
            \Illuminate\Support\Facades\DB::raw('cantidad * precio_unitario')
        );

        $this->update([
            'costo_total' => $this->costo_mano_obra + $costoRepuestos,
        ]);
    }
}
