<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import AppButton from '../../components/AppButton.vue';
import ConfirmDialog from '../../components/ConfirmDialog.vue';
import FormField from '../../components/FormField.vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    servicio: Object,
    motocicletas: Array,
    mecanicos: Array,
    repuestos: Array,
});

const form = useForm({
    motocicleta_id: props.servicio.motocicleta_id,
    mecanico_id: props.servicio.mecanico_id ?? '',
    descripcion_problema: props.servicio.descripcion_problema,
    estado: props.servicio.estado,
    costo_mano_obra: props.servicio.costo_mano_obra,
    fecha_ingreso: props.servicio.fecha_ingreso,
    fecha_entrega: props.servicio.fecha_entrega ?? '',
    observaciones: props.servicio.observaciones ?? '',
});

function submit() {
    form.put(`/servicios/${props.servicio.id}`);
}

const repuestoForm = useForm({
    repuesto_id: '',
    cantidad: 1,
});

function agregarRepuesto() {
    repuestoForm.post(`/servicios/${props.servicio.id}/repuestos`, {
        preserveScroll: true,
        onSuccess: () => repuestoForm.reset(),
    });
}

const detalleAQuitar = ref(null);

function confirmarQuitar(detalle) {
    detalleAQuitar.value = detalle;
}

function quitarRepuesto() {
    router.delete(
        `/servicios/${props.servicio.id}/repuestos/${detalleAQuitar.value.id}`,
        {
            preserveScroll: true,
            onFinish: () => (detalleAQuitar.value = null),
        },
    );
}

function moneda(valor) {
    return `$${Number(valor ?? 0).toLocaleString('es-CO')}`;
}
</script>

<template>
    <Head title="Editar Servicio" />

    <AppLayout>
        <div class="mx-auto max-w-3xl">
            <!-- Encabezado -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold text-slate-50">
                    Editar Servicio
                </h1>
                <p class="mt-1 text-sm text-slate-400">
                    Actualiza el estado del trabajo y registra los repuestos
                    usados.
                </p>
            </div>

            <!-- Tarjeta: datos del servicio -->
            <form @submit.prevent="submit" class="panel mb-8">
                <div class="grid gap-5 p-6 sm:grid-cols-2">
                    <FormField
                        label="Motocicleta"
                        :error="form.errors.motocicleta_id"
                    >
                        <select v-model="form.motocicleta_id" class="campo">
                            <option
                                v-for="moto in motocicletas"
                                :key="moto.id"
                                :value="moto.id"
                            >
                                {{ moto.placa }} — {{ moto.cliente.nombre }}
                                {{ moto.cliente.apellido }}
                            </option>
                        </select>
                    </FormField>

                    <FormField
                        label="Mecánico"
                        :error="form.errors.mecanico_id"
                    >
                        <select v-model="form.mecanico_id" class="campo">
                            <option value="">-- Sin asignar --</option>
                            <option
                                v-for="mecanico in mecanicos"
                                :key="mecanico.id"
                                :value="mecanico.id"
                            >
                                {{ mecanico.name }}
                            </option>
                        </select>
                    </FormField>

                    <div class="sm:col-span-2">
                        <FormField
                            label="Descripción del problema"
                            :error="form.errors.descripcion_problema"
                        >
                            <textarea
                                v-model="form.descripcion_problema"
                                rows="4"
                                class="campo"
                            ></textarea>
                        </FormField>
                    </div>

                    <FormField label="Estado" :error="form.errors.estado">
                        <select v-model="form.estado" class="campo">
                            <option value="pendiente">Pendiente</option>
                            <option value="en_proceso">En proceso</option>
                            <option value="terminado">Terminado</option>
                            <option value="entregado">Entregado</option>
                        </select>
                    </FormField>

                    <FormField
                        label="Costo de mano de obra"
                        :error="form.errors.costo_mano_obra"
                    >
                        <input
                            type="number"
                            step="0.01"
                            v-model="form.costo_mano_obra"
                            class="campo"
                        />
                    </FormField>

                    <FormField
                        label="Fecha de ingreso"
                        :error="form.errors.fecha_ingreso"
                    >
                        <input
                            type="date"
                            v-model="form.fecha_ingreso"
                            class="campo"
                        />
                    </FormField>

                    <FormField
                        label="Fecha de entrega"
                        :error="form.errors.fecha_entrega"
                    >
                        <input
                            type="date"
                            v-model="form.fecha_entrega"
                            class="campo"
                        />
                    </FormField>

                    <div class="sm:col-span-2">
                        <FormField
                            label="Observaciones"
                            :error="form.errors.observaciones"
                        >
                            <textarea
                                v-model="form.observaciones"
                                rows="3"
                                class="campo"
                            ></textarea>
                        </FormField>
                    </div>
                </div>

                <!-- Pie con botones -->
                <div
                    class="border-linea bg-superficie-alta/40 flex justify-end gap-3 border-t px-6 py-4"
                >
                    <AppButton href="/servicios">Cancelar</AppButton>
                    <AppButton
                        type="submit"
                        variant="primary"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Actualizando...' : 'Actualizar' }}
                    </AppButton>
                </div>
            </form>

            <!-- Tarjeta: repuestos usados -->
            <div class="panel">
                <div class="border-linea border-b px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-50">
                        Repuestos usados
                    </h2>
                    <p class="text-xs text-slate-400">
                        Al agregar o quitar un repuesto, el stock se ajusta
                        solo.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="tabla">
                        <thead>
                            <tr>
                                <th>Repuesto</th>
                                <th>Cantidad</th>
                                <th>Precio unitario</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="detalle in servicio.detalles"
                                :key="detalle.id"
                            >
                                <td class="font-medium text-slate-50">
                                    {{
                                        detalle.repuesto?.nombre ??
                                        'Repuesto eliminado'
                                    }}
                                </td>
                                <td class="tabular-nums">
                                    {{ detalle.cantidad }}
                                </td>
                                <td class="tabular-nums">
                                    {{ moneda(detalle.precio_unitario) }}
                                </td>
                                <td
                                    class="font-semibold text-slate-100 tabular-nums"
                                >
                                    {{
                                        moneda(
                                            detalle.cantidad *
                                                detalle.precio_unitario,
                                        )
                                    }}
                                </td>
                                <td>
                                    <div class="flex justify-end">
                                        <AppButton
                                            size="sm"
                                            variant="danger"
                                            @click="confirmarQuitar(detalle)"
                                        >
                                            Quitar
                                        </AppButton>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="servicio.detalles.length === 0">
                                <td
                                    colspan="5"
                                    class="py-10 text-center text-slate-400"
                                >
                                    No se han agregado repuestos a este
                                    servicio.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Costo total -->
                <div
                    class="border-linea flex items-center justify-between border-t px-6 py-4"
                >
                    <span class="text-sm text-slate-400">Costo total</span>
                    <span
                        class="text-xl font-semibold text-amber-400 tabular-nums"
                    >
                        {{ moneda(servicio.costo_total) }}
                    </span>
                </div>

                <!-- Agregar repuesto -->
                <form
                    @submit.prevent="agregarRepuesto"
                    class="border-linea bg-superficie-alta/40 border-t px-6 py-4"
                >
                    <p class="mb-3 text-sm font-medium text-slate-200">
                        Agregar repuesto
                    </p>
                    <div class="flex flex-wrap items-start gap-2">
                        <select
                            v-model="repuestoForm.repuesto_id"
                            class="campo min-w-48 flex-1"
                        >
                            <option value="">
                                -- Selecciona un repuesto --
                            </option>
                            <option
                                v-for="repuesto in repuestos"
                                :key="repuesto.id"
                                :value="repuesto.id"
                            >
                                {{ repuesto.nombre }} (stock:
                                {{ repuesto.cantidad }})
                            </option>
                        </select>
                        <input
                            type="number"
                            min="1"
                            v-model="repuestoForm.cantidad"
                            placeholder="Cantidad"
                            class="campo w-28"
                        />
                        <AppButton
                            type="submit"
                            variant="primary"
                            :disabled="repuestoForm.processing"
                        >
                            Agregar
                        </AppButton>
                    </div>
                    <p
                        v-if="repuestoForm.errors.repuesto_id"
                        class="mt-2 text-xs text-red-400"
                    >
                        {{ repuestoForm.errors.repuesto_id }}
                    </p>
                    <p
                        v-if="repuestoForm.errors.cantidad"
                        class="mt-1 text-xs text-red-400"
                    >
                        {{ repuestoForm.errors.cantidad }}
                    </p>
                </form>
            </div>
        </div>

        <ConfirmDialog
            :show="!!detalleAQuitar"
            title="Quitar repuesto"
            :message="`¿Quitar ${detalleAQuitar?.repuesto?.nombre} de este servicio? El stock se devolverá automáticamente.`"
            @confirm="quitarRepuesto"
            @cancel="detalleAQuitar = null"
        />
    </AppLayout>
</template>
