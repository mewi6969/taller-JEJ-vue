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

const inputClasses =
    'rounded-md border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100 font-sans';
</script>

<template>
    <Head title="Editar Servicio" />

    <AppLayout>
        <h1 class="mb-6 text-2xl font-semibold text-slate-100">
            Editar Servicio
        </h1>

        <form @submit.prevent="submit" class="flex max-w-lg flex-col gap-4">
            <FormField label="Motocicleta" :error="form.errors.motocicleta_id">
                <select v-model="form.motocicleta_id" :class="inputClasses">
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

            <FormField label="Mecánico" :error="form.errors.mecanico_id">
                <select v-model="form.mecanico_id" :class="inputClasses">
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

            <FormField
                label="Descripción del problema"
                :error="form.errors.descripcion_problema"
            >
                <textarea
                    v-model="form.descripcion_problema"
                    rows="4"
                    :class="inputClasses"
                ></textarea>
            </FormField>

            <FormField label="Estado" :error="form.errors.estado">
                <select v-model="form.estado" :class="inputClasses">
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
                    :class="inputClasses"
                />
            </FormField>

            <FormField
                label="Fecha de ingreso"
                :error="form.errors.fecha_ingreso"
            >
                <input
                    type="date"
                    v-model="form.fecha_ingreso"
                    :class="inputClasses"
                />
            </FormField>

            <FormField
                label="Fecha de entrega"
                :error="form.errors.fecha_entrega"
            >
                <input
                    type="date"
                    v-model="form.fecha_entrega"
                    :class="inputClasses"
                />
            </FormField>

            <FormField
                label="Observaciones"
                :error="form.errors.observaciones"
            >
                <textarea
                    v-model="form.observaciones"
                    rows="3"
                    :class="inputClasses"
                ></textarea>
            </FormField>

            <div class="mt-2 flex gap-3">
                <AppButton
                    type="submit"
                    variant="primary"
                    :disabled="form.processing"
                >
                    Actualizar
                </AppButton>
                <AppButton href="/servicios">Cancelar</AppButton>
            </div>
        </form>

        <hr class="my-8 border-slate-700" />

        <h2 class="mb-4 text-xl font-semibold text-slate-100">
            Repuestos usados
        </h2>

        <table class="mb-4 w-full max-w-2xl border-collapse">
            <thead>
                <tr>
                    <th class="border-b border-slate-700 p-3 text-left">
                        Repuesto
                    </th>
                    <th class="border-b border-slate-700 p-3 text-left">
                        Cantidad
                    </th>
                    <th class="border-b border-slate-700 p-3 text-left">
                        Precio unitario
                    </th>
                    <th class="border-b border-slate-700 p-3 text-left">
                        Subtotal
                    </th>
                    <th class="border-b border-slate-700 p-3"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="detalle in servicio.detalles" :key="detalle.id">
                    <td class="border-b border-slate-700 p-3">
                        {{ detalle.repuesto.nombre }}
                    </td>
                    <td class="border-b border-slate-700 p-3">
                        {{ detalle.cantidad }}
                    </td>
                    <td class="border-b border-slate-700 p-3">
                        ${{
                            Number(detalle.precio_unitario).toLocaleString(
                                'es-CO',
                            )
                        }}
                    </td>
                    <td class="border-b border-slate-700 p-3">
                        ${{
                            (
                                detalle.cantidad * detalle.precio_unitario
                            ).toLocaleString('es-CO')
                        }}
                    </td>
                    <td class="border-b border-slate-700 p-3">
                        <AppButton
                            size="sm"
                            variant="danger"
                            @click="confirmarQuitar(detalle)"
                        >
                            Quitar
                        </AppButton>
                    </td>
                </tr>
                <tr v-if="servicio.detalles.length === 0">
                    <td colspan="5" class="p-3 text-center text-slate-400">
                        No se han agregado repuestos a este servicio.
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="mb-6 text-lg text-slate-100">
            Costo total:
            <strong
                >${{
                    Number(servicio.costo_total).toLocaleString('es-CO')
                }}</strong
            >
        </p>

        <form
            @submit.prevent="agregarRepuesto"
            class="flex max-w-lg items-start gap-2"
        >
            <select
                v-model="repuestoForm.repuesto_id"
                :class="[inputClasses, 'flex-1']"
            >
                <option value="">-- Selecciona un repuesto --</option>
                <option
                    v-for="repuesto in repuestos"
                    :key="repuesto.id"
                    :value="repuesto.id"
                >
                    {{ repuesto.nombre }} (stock: {{ repuesto.cantidad }})
                </option>
            </select>
            <input
                type="number"
                min="1"
                v-model="repuestoForm.cantidad"
                placeholder="Cantidad"
                :class="[inputClasses, 'w-28']"
            />
            <AppButton
                type="submit"
                variant="primary"
                :disabled="repuestoForm.processing"
            >
                Agregar
            </AppButton>
        </form>
        <span
            v-if="repuestoForm.errors.repuesto_id"
            class="mt-2 block text-sm text-red-400"
        >
            {{ repuestoForm.errors.repuesto_id }}
        </span>
        <span
            v-if="repuestoForm.errors.cantidad"
            class="mt-1 block text-sm text-red-400"
        >
            {{ repuestoForm.errors.cantidad }}
        </span>

        <ConfirmDialog
            :show="!!detalleAQuitar"
            title="Quitar repuesto"
            :message="`¿Quitar ${detalleAQuitar?.repuesto?.nombre} de este servicio? El stock se devolverá automáticamente.`"
            @confirm="quitarRepuesto"
            @cancel="detalleAQuitar = null"
        />
    </AppLayout>
</template>
