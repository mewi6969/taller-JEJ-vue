<script setup>
import { Head, useForm } from '@inertiajs/vue3';

import AppButton from '../../components/AppButton.vue';
import FormField from '../../components/FormField.vue';
import AppLayout from '../../layouts/AppLayout.vue';

const form = useForm({
    nombre: '',
    apellido: '',
    documento: '',
    telefono: '',
    email: '',
    direccion: '',
});

function submit() {
    form.post('/clientes');
}
</script>

<template>
    <Head title="Nuevo Cliente" />

    <AppLayout>
        <div class="mx-auto max-w-2xl">
            <!-- Encabezado -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold text-slate-50">
                    Nuevo Cliente
                </h1>
                <p class="mt-1 text-sm text-slate-400">
                    Registra los datos del cliente para poder asociarle
                    motocicletas y servicios.
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
                    class="flex justify-end gap-3 border-t border-linea bg-superficie-alta/40 px-6 py-4"
                >
                    <AppButton href="/clientes">Cancelar</AppButton>
                    <AppButton
                        type="submit"
                        variant="primary"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Guardando...' : 'Guardar' }}
                    </AppButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
