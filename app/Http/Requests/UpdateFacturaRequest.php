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

        if ($this->esPago('efectivo')) {
            $reglasMonto = ['required', 'numeric', 'min:'.$factura->total];
        }

        $reglasUltimos4 = ['nullable'];
        $reglasAprobacion = ['nullable'];

        if ($this->esPago('tarjeta')) {
            $reglasUltimos4 = ['required', 'digits:4'];
            $reglasAprobacion = ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9-]+$/'];
        }

        return [
            'estado' => ['required', Rule::in(['pendiente', 'pagada', 'anulada'])],
            'metodo_pago' => [
                'nullable',
                'required_if:estado,pagada',
                Rule::in(['efectivo', 'transferencia', 'tarjeta']),
            ],
            'monto_recibido' => $reglasMonto,
            'tarjeta_ultimos4' => $reglasUltimos4,
            'tarjeta_aprobacion' => $reglasAprobacion,
            'fecha_pago' => ['nullable', 'required_if:estado,pagada', 'date', 'before_or_equal:today'],
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
            'tarjeta_ultimos4.required' => 'Indica los últimos 4 dígitos de la tarjeta.',
            'tarjeta_ultimos4.digits' => 'Escribe solo los 4 últimos dígitos de la tarjeta, nunca el número completo.',
            'tarjeta_aprobacion.required' => 'Indica el número de aprobación del voucher.',
            'tarjeta_aprobacion.regex' => 'El número de aprobación solo puede tener letras, números y guiones.',
            'fecha_pago.before_or_equal' => 'La fecha de pago no puede ser futura.',
        ];
    }

    private function esPago(string $metodo): bool
    {
        return $this->input('estado') === 'pagada'
            && $this->input('metodo_pago') === $metodo;
    }
}
