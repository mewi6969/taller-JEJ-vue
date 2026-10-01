<script setup>
import { Head, useForm } from '@inertiajs/vue3';

import AppButton from '../../components/AppButton.vue';
import FormField from '../../components/FormField.vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    motocicleta: Object,
    clientes: Array,
});

const form = useForm({
    cliente_id: props.motocicleta.cliente_id,
    placa: props.motocicleta.placa,
    marca: props.motocicleta.marca,
    modelo: props.motocicleta.modelo,
    anio: props.motocicleta.anio,
    cilindraje: props.motocicleta.cilindraje,
    color: props.motocicleta.color,
});

function submit() {
    form.put(`/motocicletas/${props.motocicleta.id}`);
}

const inputClasses =
    'rounded-md border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100';
</script>

<template>
    <Head title="Editar Motocicleta" />

    <AppLayout>
        <h1 class="mb-6 text-2xl font-semibold text-slate-100">
            Editar Motocicleta
        </h1>

        <form @submit.prevent="submit" class="flex max-w-md flex-col gap-4">
            <FormField label="Cliente" :error="form.errors.cliente_id">
                <select v-model="form.cliente_id" :class="inputClasses">
                    <option
                        v-for="cliente in clientes"
                        :key="cliente.id"
                        :value="cliente.id"
                    >
                        {{ cliente.nombre }} {{ cliente.apellido }} —
                        {{ cliente.documento }}
                    </option>
                </select>
            </FormField>

            <FormField label="Placa" :error="form.errors.placa">
                <input
                    type="text"
                    v-model="form.placa"
                    :class="inputClasses"
                />
            </FormField>

            <FormField label="Marca" :error="form.errors.marca">
                <input
                    type="text"
                    v-model="form.marca"
                    :class="inputClasses"
                />
            </FormField>

            <FormField label="Modelo" :error="form.errors.modelo">
                <input
                    type="text"
                    v-model="form.modelo"
                    :class="inputClasses"
                />
            </FormField>

            <FormField label="Año" :error="form.errors.anio">
                <input
                    type="number"
                    v-model="form.anio"
                    :class="inputClasses"
                />
            </FormField>

            <FormField label="Cilindraje" :error="form.errors.cilindraje">
                <input
                    type="number"
                    v-model="form.cilindraje"
                    :class="inputClasses"
                />
            </FormField>

            <FormField label="Color" :error="form.errors.color">
                <input
                    type="text"
                    v-model="form.color"
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
                <AppButton href="/motocicletas">Cancelar</AppButton>
            </div>
        </form>
    </AppLayout>
</template>
