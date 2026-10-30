<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('cliente'));
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'documento' => [
                'required',
                'string',
                'max:30',
                Rule::unique('clientes', 'documento')
                    ->ignore($this->route('cliente'))
                    ->whereNull('deleted_at'),
            ],
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:255',
        ];
    }
}
