<?php

namespace App\Http\Requests;

use App\Models\Factura;
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
        /** @var Factura $factura */
        $factura = $this->route('factura');

        $reglasMonto = ['nullable', 'numeric'];

        if ($this->esPagoEfectivo()) {
            $reglasMonto = ['required', 'numeric', 'min:'.$factura->total];
        }

        return [
            'estado' => ['required', Rule::in(['pendiente', 'pagada', 'anulada'])],
            'metodo_pago' => [
                'nullable',
                'required_if:estado,pagada',
                Rule::in(['efectivo', 'transferencia', 'tarjeta']),
            ],
            'monto_recibido' => $reglasMonto,
            'fecha_pago' => ['nullable', 'required_if:estado,pagada', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'monto_recibido.required' => 'Indica cuánto entregó el cliente.',
            'monto_recibido.min' => 'El monto recibido no alcanza para cubrir el total de la factura.',
        ];
    }

    private function esPagoEfectivo(): bool
    {
        return $this->input('estado') === 'pagada'
            && $this->input('metodo_pago') === 'efectivo';
    }
}
