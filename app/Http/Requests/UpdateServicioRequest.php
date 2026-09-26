<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('servicio'));
    }

    public function rules(): array
    {
        return [
            'motocicleta_id' => ['required', 'exists:motocicletas,id'],
            'mecanico_id' => ['nullable', 'exists:users,id'],
            'descripcion_problema' => ['required', 'string'],
            'estado' => ['required', 'in:pendiente,en_proceso,terminado,entregado'],
            'costo_mano_obra' => ['required', 'numeric', 'min:0'],
            'fecha_ingreso' => ['nullable', 'date'],
            'fecha_entrega' => ['nullable', 'date', 'after_or_equal:fecha_ingreso'],
            'observaciones' => ['nullable', 'string'],
        ];
    }
}
