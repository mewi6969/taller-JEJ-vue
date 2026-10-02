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
</script>

<template>
    <Head title="Editar Cliente" />

    <AppLayout>
        <div class="mx-auto max-w-2xl">
            <!-- Encabezado -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold text-slate-50">
                    Editar Cliente
                </h1>
                <p class="mt-1 text-sm text-slate-400">
                    Modifica los datos de
                    <span class="font-medium text-slate-200">
                        {{ cliente.nombre }} {{ cliente.apellido }}
                    </span>
                    .
                </p>
            </div>

            <!-- Tarjeta del formulario -->
            <form @submit.prevent="submit" class="panel">
                <div class="grid gap-5 p-6 sm:grid-cols-2">
                    <FormField label="Nombre" :error="form.errors.nombre">
                        <input
                            type="text"
                            v-model="form.nombre"
                            class="campo"
                        />
                    </FormField>

                    <FormField label="Apellido" :error="form.errors.apellido">
                        <input
                            type="text"
                            v-model="form.apellido"
                            class="campo"
                        />
                    </FormField>

                    <FormField label="Documento" :error="form.errors.documento">
                        <input
                            type="text"
                            v-model="form.documento"
                            class="campo"
                        />
                    </FormField>

                    <FormField label="Teléfono" :error="form.errors.telefono">
                        <input
                            type="text"
                            v-model="form.telefono"
                            class="campo"
                        />
                    </FormField>

                    <div class="sm:col-span-2">
                        <FormField
                            label="Email"
                            :error="form.errors.email"
                            ayuda="Se usa para avisar cuando el servicio de su moto esté terminado."
                        >
                            <input
                                type="email"
                                v-model="form.email"
                                class="campo"
                            />
                        </FormField>
                    </div>

                    <div class="sm:col-span-2">
                        <FormField
                            label="Dirección"
                            :error="form.errors.direccion"
                        >
                            <input
                                type="text"
                                v-model="form.direccion"
                                class="campo"
                            />
                        </FormField>
                    </div>
                </div>

                <!-- Pie con botones -->
                <div
                    class="border-linea bg-superficie-alta/40 flex justify-end gap-3 border-t px-6 py-4"
                >
                    <AppButton href="/clientes">Cancelar</AppButton>
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
