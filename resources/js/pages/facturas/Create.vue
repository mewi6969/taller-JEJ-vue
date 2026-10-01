<script setup>
import { Head, useForm } from '@inertiajs/vue3';

import AppButton from '../../components/AppButton.vue';
import FormField from '../../components/FormField.vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    servicios: Array,
});

const form = useForm({
    servicio_id: '',
    descuento: 0,
});

function submit() {
    form.post('/facturas');
}

const inputClasses =
    'rounded-md border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100';
</script>

<template>
    <Head title="Nueva Factura" />

    <AppLayout>
        <h1 class="mb-6 text-2xl font-semibold text-slate-100">
            Nueva Factura
        </h1>

        <form @submit.prevent="submit" class="flex max-w-md flex-col gap-4">
            <FormField label="Servicio" :error="form.errors.servicio_id">
                <select v-model="form.servicio_id" :class="inputClasses">
                    <option value="" disabled>Selecciona un servicio</option>
                    <option
                        v-for="servicio in servicios"
                        :key="servicio.id"
                        :value="servicio.id"
                    >
                        {{ servicio.motocicleta.cliente.nombre }}
                        {{ servicio.motocicleta.cliente.apellido }} -
                        {{ servicio.motocicleta.placa }} - ${{
                            Number(servicio.costo_total).toLocaleString(
                                'es-CO',
                            )
                        }}
                    </option>
                </select>
                <span
                    v-if="servicios.length === 0"
                    class="text-sm text-slate-400"
                >
                    No hay servicios terminados pendientes de facturar.
                </span>
            </FormField>

            <FormField
                label="Descuento (opcional)"
                :error="form.errors.descuento"
            >
                <input
                    type="number"
                    step="0.01"
                    v-model="form.descuento"
                    :class="inputClasses"
                />
            </FormField>

            <div class="mt-2 flex gap-3">
                <AppButton
                    type="submit"
                    variant="primary"
                    :disabled="form.processing || servicios.length === 0"
                >
                    Generar factura
                </AppButton>
                <AppButton href="/facturas">Cancelar</AppButton>
            </div>
        </form>
    </AppLayout>
</template>
