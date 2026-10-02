<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

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

const servicioSeleccionado = computed(() =>
    props.servicios.find((servicio) => servicio.id === form.servicio_id),
);

function moneda(valor) {
    return `$${Number(valor ?? 0).toLocaleString('es-CO')}`;
}

function submit() {
    form.post('/facturas');
}
</script>

<template>
    <Head title="Nueva Factura" />

    <AppLayout>
        <div class="mx-auto max-w-2xl">
            <!-- Encabezado -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold text-slate-50">
                    Nueva Factura
                </h1>
                <p class="mt-1 text-sm text-slate-400">
                    Genera la factura de un servicio terminado.
                </p>
            </div>

            <!-- Tarjeta del formulario -->
            <form @submit.prevent="submit" class="panel">
                <div class="grid gap-5 p-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <FormField
                            label="Servicio"
                            :error="form.errors.servicio_id"
                        >
                            <select v-model="form.servicio_id" class="campo">
                                <option value="" disabled>
                                    Selecciona un servicio
                                </option>
                                <option
                                    v-for="servicio in servicios"
                                    :key="servicio.id"
                                    :value="servicio.id"
                                >
                                    {{ servicio.motocicleta.cliente.nombre }}
                                    {{ servicio.motocicleta.cliente.apellido }}
                                    - {{ servicio.motocicleta.placa }} -
                                    {{ moneda(servicio.costo_total) }}
                                </option>
                            </select>
                        </FormField>

                        <p
                            v-if="servicios.length === 0"
                            class="mt-3 rounded-lg border border-amber-500/30 bg-amber-500/10 px-3 py-2 text-sm text-amber-300"
                        >
                            No hay servicios terminados pendientes de facturar.
                        </p>
                    </div>

                    <!-- Resumen del servicio elegido -->
                    <div
                        v-if="servicioSeleccionado"
                        class="border-linea bg-fondo/50 rounded-lg border p-4 sm:col-span-2"
                    >
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p
                                    class="text-xs font-semibold tracking-wider text-slate-400 uppercase"
                                >
                                    Cliente
                                </p>
                                <p class="mt-1 font-medium text-slate-50">
                                    {{
                                        servicioSeleccionado.motocicleta.cliente
                                            .nombre
                                    }}
                                    {{
                                        servicioSeleccionado.motocicleta.cliente
                                            .apellido
                                    }}
                                </p>
                            </div>
                            <span
                                class="rounded-md border border-amber-500/40 bg-amber-500/10 px-2.5 py-1 font-mono text-xs font-semibold tracking-widest text-amber-400 uppercase"
                            >
                                {{ servicioSeleccionado.motocicleta.placa }}
                            </span>
                            <div class="text-right">
                                <p
                                    class="text-xs font-semibold tracking-wider text-slate-400 uppercase"
                                >
                                    Costo del servicio
                                </p>
                                <p
                                    class="mt-1 text-lg font-semibold text-slate-50 tabular-nums"
                                >
                                    {{
                                        moneda(servicioSeleccionado.costo_total)
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <FormField
                            label="Descuento (opcional)"
                            :error="form.errors.descuento"
                            ayuda="Valor en pesos que se resta del total de la factura."
                        >
                            <input
                                type="number"
                                step="0.01"
                                v-model="form.descuento"
                                class="campo"
                            />
                        </FormField>
                    </div>
                </div>

                <!-- Pie con botones -->
                <div
                    class="border-linea bg-superficie-alta/40 flex justify-end gap-3 border-t px-6 py-4"
                >
                    <AppButton href="/facturas">Cancelar</AppButton>
                    <AppButton
                        type="submit"
                        variant="primary"
                        :disabled="form.processing || servicios.length === 0"
                    >
                        {{
                            form.processing ? 'Generando...' : 'Generar factura'
                        }}
                    </AppButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
