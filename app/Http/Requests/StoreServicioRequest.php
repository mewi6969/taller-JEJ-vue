<?php

namespace App\Http\Requests;

use App\Models\Servicio;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Servicio::class);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
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
