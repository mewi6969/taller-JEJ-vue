<script setup>
import { Head, useForm } from '@inertiajs/vue3';

import AppButton from '../../components/AppButton.vue';
import FormField from '../../components/FormField.vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    cliente: Object,
});

const form = useForm({
    nombre: props.cliente.nombre,
    apellido: props.cliente.apellido,
    documento: props.cliente.documento,
    telefono: props.cliente.telefono,
    email: props.cliente.email,
    direccion: props.cliente.direccion,
});

function submit() {
    form.put(`/clientes/${props.cliente.id}`);
}

const inputClasses =
    'rounded-md border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100';
</script>

<template>
    <Head title="Editar Cliente" />

    <AppLayout>
        <h1 class="mb-6 text-2xl font-semibold text-slate-100">
            Editar Cliente
        </h1>

        <form @submit.prevent="submit" class="flex max-w-md flex-col gap-4">
            <FormField label="Nombre" :error="form.errors.nombre">
                <input
                    type="text"
                    v-model="form.nombre"
                    :class="inputClasses"
                />
            </FormField>

            <FormField label="Apellido" :error="form.errors.apellido">
                <input
                    type="text"
                    v-model="form.apellido"
                    :class="inputClasses"
                />
            </FormField>

            <FormField label="Documento" :error="form.errors.documento">
                <input
                    type="text"
                    v-model="form.documento"
                    :class="inputClasses"
                />
            </FormField>

            <FormField label="Teléfono" :error="form.errors.telefono">
                <input
                    type="text"
                    v-model="form.telefono"
                    :class="inputClasses"
                />
            </FormField>

            <FormField label="Email" :error="form.errors.email">
                <input
                    type="email"
                    v-model="form.email"
                    :class="inputClasses"
                />
            </FormField>

            <FormField label="Dirección" :error="form.errors.direccion">
                <input
                    type="text"
                    v-model="form.direccion"
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
                <AppButton href="/clientes">Cancelar</AppButton>
            </div>
        </form>
    </AppLayout>
</template>
