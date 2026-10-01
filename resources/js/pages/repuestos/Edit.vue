<script setup>
import { Head, useForm } from '@inertiajs/vue3';

import AppButton from '../../components/AppButton.vue';
import FormField from '../../components/FormField.vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    repuesto: Object,
});

const form = useForm({
    nombre: props.repuesto.nombre,
    descripcion: props.repuesto.descripcion,
    precio: props.repuesto.precio,
    cantidad: props.repuesto.cantidad,
    cantidad_minima: props.repuesto.cantidad_minima,
});

function submit() {
    form.put(`/repuestos/${props.repuesto.id}`);
}

const inputClasses =
    'rounded-md border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100';
</script>

<template>
    <Head title="Editar Repuesto" />

    <AppLayout>
        <h1 class="mb-6 text-2xl font-semibold text-slate-100">
            Editar Repuesto
        </h1>

        <form @submit.prevent="submit" class="flex max-w-md flex-col gap-4">
            <FormField label="Nombre" :error="form.errors.nombre">
                <input
                    type="text"
                    v-model="form.nombre"
                    :class="inputClasses"
                />
            </FormField>

            <FormField label="Descripción" :error="form.errors.descripcion">
                <input
                    type="text"
                    v-model="form.descripcion"
                    :class="inputClasses"
                />
            </FormField>

            <FormField label="Precio" :error="form.errors.precio">
                <input
                    type="number"
                    step="0.01"
                    v-model="form.precio"
                    :class="inputClasses"
                />
            </FormField>

            <FormField label="Cantidad" :error="form.errors.cantidad">
                <input
                    type="number"
                    v-model="form.cantidad"
                    :class="inputClasses"
                />
            </FormField>

            <FormField
                label="Cantidad mínima"
                :error="form.errors.cantidad_minima"
            >
                <input
                    type="number"
                    v-model="form.cantidad_minima"
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
                <AppButton href="/repuestos">Cancelar</AppButton>
            </div>
        </form>
    </AppLayout>
</template>
