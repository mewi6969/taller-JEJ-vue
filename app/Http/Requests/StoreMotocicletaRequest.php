<?php

namespace App\Http\Requests;

use App\Models\Motocicleta;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMotocicletaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Motocicleta::class);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'placa' => 'required|string|max:20|unique:motocicletas,placa',
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'anio' => 'nullable|integer|min:1980|max:'.(date('Y') + 1),
            'cilindraje' => 'nullable|integer|min:0',
            'color' => 'nullable|string|max:100',
        ];
    }
}
