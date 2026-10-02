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
    fecha_pago: props.factura.fecha_pago
        ? props.factura.fecha_pago.split('T')[0]
        : '',
});

const requierePago = computed(() => form.estado === 'pagada');

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
                        ${{ Number(factura.total).toLocaleString('es-CO') }}
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
