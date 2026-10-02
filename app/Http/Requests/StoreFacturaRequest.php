<?php

namespace App\Http\Requests;

use App\Models\Servicio;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFacturaRequest extends FormRequest
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
            'servicio_id' => [
                'required',
                'exists:servicios,id',
                Rule::unique('facturas', 'servicio_id'),
            ],
            'descuento' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $servicio = Servicio::find($this->integer('servicio_id'));

            if ($servicio && $servicio->estado !== 'terminado') {
                $validator->errors()->add('servicio_id', 'Solo se pueden facturar servicios terminados.');
            }
        });
    }
}
