<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'estado' => ['required', Rule::in(['pendiente', 'pagada', 'anulada'])],
            'metodo_pago' => [
                'nullable',
                'required_if:estado,pagada',
                Rule::in(['efectivo', 'transferencia', 'tarjeta']),
            ],
            'fecha_pago' => ['nullable', 'required_if:estado,pagada', 'date'],
        ];
    }
}
