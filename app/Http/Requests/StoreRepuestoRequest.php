<?php

namespace App\Http\Requests;

use App\Models\Repuesto;
use Illuminate\Foundation\Http\FormRequest;

class StoreRepuestoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Repuesto::class);
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'precio' => ['required', 'numeric', 'min:0'],
            'cantidad' => ['required', 'integer', 'min:0'],
            'cantidad_minima' => ['required', 'integer', 'min:0'],
        ];
    }
}
