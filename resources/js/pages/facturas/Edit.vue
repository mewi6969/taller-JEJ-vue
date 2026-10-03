<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

import AppBadge from '../../components/AppBadge.vue';
import AppButton from '../../components/AppButton.vue';
import FormField from '../../components/FormField.vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    factura: Object,
});

const form = useForm({
    estado: props.factura.estado,
    metodo_pago: props.factura.metodo_pago || '',
    monto_recibido: props.factura.monto_recibido ?? '',
    tarjeta_ultimos4: props.factura.tarjeta_ultimos4 ?? '',
    tarjeta_aprobacion: props.factura.tarjeta_aprobacion ?? '',
    fecha_pago: props.factura.fecha_pago
        ? props.factura.fecha_pago.split('T')[0]
        : '',
});

const requierePago = computed(() => form.estado === 'pagada');
const esEfectivo = computed(
    () => requierePago.value && form.metodo_pago === 'efectivo',
);

const esTransferencia = computed(
    () => requierePago.value && form.metodo_pago === 'transferencia',
);

const esTarjeta = computed(
    () => requierePago.value && form.metodo_pago === 'tarjeta',
);

// Diferencia entre lo que entregó el cliente y el total de la factura
const diferencia = computed(() => {
    if (form.monto_recibido === '' || form.monto_recibido === null) {
        return null;
    }

    return Number(form.monto_recibido) - Number(props.factura.total);
});

function dinero(valor) {
    return `$${Number(valor).toLocaleString('es-CO')}`;
}

function badgeVariant(estado) {
    if (estado === 'pagada') return 'ok';
    if (estado === 'anulada') return 'danger';
    return 'warning';
}

function submit() {
    form.put(`/facturas/${props.factura.id}`);
}
</script>

<template>
    <Head title="Editar Factura" />

    <AppLayout>
        <div class="mx-auto max-w-2xl">
            <!-- Encabezado -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold text-slate-50">
                    Factura
                    <span class="font-mono text-amber-400">
                        {{ factura.numero_factura }}
                    </span>
                </h1>
                <p class="mt-1 text-sm text-slate-400">
                    Actualiza el estado y los datos de pago.
                </p>
            </div>

            <!-- Resumen de la factura -->
            <div class="panel mb-6 grid gap-5 p-6 sm:grid-cols-3">
                <div>
                    <p
                        class="text-xs font-semibold tracking-wider text-slate-400 uppercase"
                    >
                        Cliente
                    </p>
                    <p class="mt-1 font-medium text-slate-50">
                        {{ factura.servicio.motocicleta.cliente.nombre }}
                        {{ factura.servicio.motocicleta.cliente.apellido }}
                    </p>
                </div>
                <div>
                    <p
                        class="text-xs font-semibold tracking-wider text-slate-400 uppercase"
                    >
                        Motocicleta
                    </p>
                    <p class="mt-1">
                        <span
                            class="rounded-md border border-amber-500/40 bg-amber-500/10 px-2.5 py-1 font-mono text-xs font-semibold tracking-widest text-amber-400 uppercase"
                        >
                            {{ factura.servicio.motocicleta.placa }}
                        </span>
                    </p>
                </div>
                <div>
                    <p
                        class="text-xs font-semibold tracking-wider text-slate-400 uppercase"
                    >
                        Total
                    </p>
                    <p
                        class="mt-1 text-xl font-semibold text-slate-50 tabular-nums"
                    >
                        {{ dinero(factura.total) }}
                    </p>
                </div>
            </div>

            <!-- Tarjeta del formulario -->
            <form @submit.prevent="submit" class="panel">
                <div class="grid gap-5 p-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <FormField label="Estado" :error="form.errors.estado">
                            <select v-model="form.estado" class="campo">
                                <option value="pendiente">Pendiente</option>
                                <option value="pagada">Pagada</option>
                                <option value="anulada">Anulada</option>
                            </select>
                        </FormField>
                        <div
                            class="mt-2 flex items-center gap-2 text-xs text-slate-500"
                        >
                            Estado actual:
                            <AppBadge :variant="badgeVariant(factura.estado)">
                                <span class="capitalize">
                                    {{ factura.estado }}
                                </span>
                            </AppBadge>
                        </div>
                    </div>

                    <FormField
                        v-if="requierePago"
                        label="Método de pago"
                        :error="form.errors.metodo_pago"
                    >
                        <select v-model="form.metodo_pago" class="campo">
                            <option value="" disabled>
                                Selecciona un método
                            </option>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="tarjeta">Tarjeta</option>
                        </select>
                    </FormField>

                    <FormField
                        v-if="requierePago"
                        label="Fecha de pago"
                        :error="form.errors.fecha_pago"
                    >
                        <input
                            type="date"
                            v-model="form.fecha_pago"
                            class="campo"
                        />
                    </FormField>

                    <!-- Pago en efectivo: monto recibido y devuelta -->
                    <div v-if="esEfectivo" class="sm:col-span-2">
                        <FormField
                            label="Monto recibido"
                            :error="form.errors.monto_recibido"
                        >
                            <input
                                type="number"
                                min="0"
                                step="1"
                                v-model="form.monto_recibido"
                                class="campo"
                                placeholder="¿Cuánto entregó el cliente?"
                            />
                        </FormField>

                        <div
                            v-if="diferencia !== null"
                            class="mt-3 rounded-lg border px-4 py-3 text-sm"
                            :class="
                                diferencia >= 0
                                    ? 'border-emerald-500/40 bg-emerald-950 text-emerald-200'
                                    : 'border-red-500/40 bg-red-950 text-red-200'
                            "
                        >
                            <template v-if="diferencia >= 0">
                                Devuelta al cliente:
                                <strong class="tabular-nums">
                                    {{ dinero(diferencia) }}
                                </strong>
                            </template>
                            <template v-else>
                                El monto no alcanza. Faltan
                                <strong class="tabular-nums">
                                    {{ dinero(Math.abs(diferencia)) }}
                                </strong>
                            </template>
                        </div>
                    </div>

                    <!-- Pago por transferencia: QR -->
                    <div v-if="esTransferencia" class="sm:col-span-2">
                        <div
                            class="border-linea flex flex-col items-center gap-3 rounded-lg border bg-white p-4 text-center"
                        >
                            <img
                                src="/images/pagos/qr-transferencia.png"
                                alt="QR para pagar por transferencia"
                                class="w-56"
                            />
                            <p class="text-sm text-slate-700">
                                Escanea para pagar
                                <strong class="tabular-nums">
                                    {{ dinero(factura.total) }}
                                </strong>
                            </p>
                        </div>
                    </div>

                    <!-- Pago con tarjeta: datos del voucher -->
                    <template v-if="esTarjeta">
                        <FormField
                            label="Últimos 4 dígitos de la tarjeta"
                            :error="form.errors.tarjeta_ultimos4"
                            ayuda="Solo los 4 últimos. Nunca escribas el número completo."
                        >
                            <input
                                type="text"
                                inputmode="numeric"
                                maxlength="4"
                                v-model="form.tarjeta_ultimos4"
                                class="campo"
                                placeholder="1234"
                            />
                        </FormField>

                        <FormField
                            label="Número de aprobación"
                            :error="form.errors.tarjeta_aprobacion"
                            ayuda="Aparece en el voucher del datáfono."
                        >
                            <input
                                type="text"
                                maxlength="20"
                                v-model="form.tarjeta_aprobacion"
                                class="campo"
                            />
                        </FormField>
                    </template>
                </div>

                <!-- Pie con botones -->
                <div
                    class="border-linea bg-superficie-alta/40 flex justify-end gap-3 border-t px-6 py-4"
                >
                    <AppButton href="/facturas">Cancelar</AppButton>
                    <AppButton
                        type="submit"
                        variant="primary"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Actualizando...' : 'Actualizar' }}
                    </AppButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
