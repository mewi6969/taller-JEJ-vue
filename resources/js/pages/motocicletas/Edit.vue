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
</script>

<template>
    <Head title="Editar Motocicleta" />

    <AppLayout>
        <div class="mx-auto max-w-2xl">
            <!-- Encabezado -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold text-slate-50">
                    Editar Motocicleta
                </h1>
                <p class="mt-1 text-sm text-slate-400">
                    Modifica los datos de la moto con placa
                    <span
                        class="rounded-md border border-amber-500/40 bg-amber-500/10 px-2 py-0.5 font-mono text-xs font-semibold tracking-widest text-amber-400 uppercase"
                    >
                        {{ motocicleta.placa }}
                    </span>
                </p>
            </div>

            <!-- Tarjeta del formulario -->
            <form @submit.prevent="submit" class="panel">
                <div class="grid gap-5 p-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <FormField
                            label="Cliente"
                            :error="form.errors.cliente_id"
                        >
                            <select v-model="form.cliente_id" class="campo">
                                <option
                                    v-for="cliente in clientes"
                                    :key="cliente.id"
                                    :value="cliente.id"
                                >
                                    {{ cliente.nombre }}
                                    {{ cliente.apellido }} —
                                    {{ cliente.documento }}
                                </option>
                            </select>
                        </FormField>
                    </div>

                    <FormField label="Placa" :error="form.errors.placa">
                        <input type="text" v-model="form.placa" class="campo" />
                    </FormField>

                    <FormField label="Marca" :error="form.errors.marca">
                        <input type="text" v-model="form.marca" class="campo" />
                    </FormField>

                    <FormField label="Modelo" :error="form.errors.modelo">
                        <input
                            type="text"
                            v-model="form.modelo"
                            class="campo"
                        />
                    </FormField>

                    <FormField label="Año" :error="form.errors.anio">
                        <input
                            type="number"
                            v-model="form.anio"
                            class="campo"
                        />
                    </FormField>

                    <FormField
                        label="Cilindraje"
                        :error="form.errors.cilindraje"
                        ayuda="En centímetros cúbicos (cc)."
                    >
                        <input
                            type="number"
                            v-model="form.cilindraje"
                            class="campo"
                        />
                    </FormField>

                    <FormField label="Color" :error="form.errors.color">
                        <input type="text" v-model="form.color" class="campo" />
                    </FormField>
                </div>

                <!-- Pie con botones -->
                <div
                    class="border-linea bg-superficie-alta/40 flex justify-end gap-3 border-t px-6 py-4"
                >
                    <AppButton href="/motocicletas">Cancelar</AppButton>
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
