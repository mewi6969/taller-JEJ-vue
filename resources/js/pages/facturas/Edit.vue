<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

import AppButton from '../../components/AppButton.vue';
import FormField from '../../components/FormField.vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    factura: Object,
});

const form = useForm({
    estado: props.factura.estado,
    metodo_pago: props.factura.metodo_pago || '',
    fecha_pago: props.factura.fecha_pago
        ? props.factura.fecha_pago.split('T')[0]
        : '',
});

const requierePago = computed(() => form.estado === 'pagada');

function submit() {
    form.put(`/facturas/${props.factura.id}`);
}

const inputClasses =
    'rounded-md border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100';
</script>

<template>
    <Head title="Editar Factura" />

    <AppLayout>
        <h1 class="mb-2 text-2xl font-semibold text-slate-100">
            Factura {{ factura.numero_factura }}
        </h1>

        <p class="mb-6 leading-relaxed text-slate-400">
            Cliente: {{ factura.servicio.motocicleta.cliente.nombre }}
            {{ factura.servicio.motocicleta.cliente.apellido }}<br />
            Motocicleta: {{ factura.servicio.motocicleta.placa }}<br />
            Total: ${{ Number(factura.total).toLocaleString('es-CO') }}
        </p>

        <form @submit.prevent="submit" class="flex max-w-md flex-col gap-4">
            <FormField label="Estado" :error="form.errors.estado">
                <select v-model="form.estado" :class="inputClasses">
                    <option value="pendiente">Pendiente</option>
                    <option value="pagada">Pagada</option>
                    <option value="anulada">Anulada</option>
                </select>
            </FormField>

            <FormField
                v-if="requierePago"
                label="Método de pago"
                :error="form.errors.metodo_pago"
            >
                <select v-model="form.metodo_pago" :class="inputClasses">
                    <option value="" disabled>Selecciona un método</option>
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
                    :class="inputClasses"
                />
            </FormField>

            <div class="mt-2 flex gap-3">
                <AppButton
                    type="submit"
                    variant="primary"
                    :disabled="form.processing"
                >
                    Actualizar
                </AppButton>
                <AppButton href="/facturas">Cancelar</AppButton>
            </div>
        </form>
    </AppLayout>
</template>
