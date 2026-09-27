<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMotocicletaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('motocicleta'));
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'placa' => 'required|string|max:20|unique:motocicletas,placa,'.$this->route('motocicleta')->id,
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'anio' => 'nullable|integer|min:1980|max:'.(date('Y') + 1),
            'cilindraje' => 'nullable|integer|min:0',
            'color' => 'nullable|string|max:100',
        ];
    }
}
