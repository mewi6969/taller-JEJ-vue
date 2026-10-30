<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    protected $fillable = ['cliente_id', 'motocicleta_id', 'estado', 'nota'];

    /** @return BelongsTo<Cliente, $this> */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /** @return BelongsTo<Motocicleta, $this> */
    public function motocicleta(): BelongsTo
    {
        return $this->belongsTo(Motocicleta::class)->withTrashed();
    }

    /** @return HasMany<PedidoDetalle, $this> */
    public function detalles(): HasMany
    {
        return $this->hasMany(PedidoDetalle::class);
    }
}
