<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Servicio::class);
    }

    public function rules(): array
    {
        return [
            'motocicleta_id' => ['required', 'exists:motocicletas,id'],
            'mecanico_id' => ['nullable', 'exists:users,id'],
            'descripcion_problema' => ['required', 'string'],
            'costo_mano_obra' => ['required', 'numeric', 'min:0'],
            'fecha_ingreso' => ['nullable', 'date'],
        ];
    }
}
