<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('usuario'));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($this->route('usuario')),
            ],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'rol' => ['required', 'in:admin,recepcionista,mecanico'],
            'puede_crear_servicios' => ['boolean'],
        ];
    }
}
