<script setup>
import { Head, useForm } from '@inertiajs/vue3';

import AppButton from '../../components/AppButton.vue';
import FormField from '../../components/FormField.vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    motocicletas: Array,
    mecanicos: Array,
});

const form = useForm({
    motocicleta_id: '',
    mecanico_id: '',
    descripcion_problema: '',
    costo_mano_obra: '',
    fecha_ingreso: '',
});

function submit() {
    form.post('/servicios');
}

const inputClasses =
    'rounded-md border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100 font-sans';
</script>

<template>
    <Head title="Nuevo Servicio" />

    <AppLayout>
        <h1 class="mb-6 text-2xl font-semibold text-slate-100">
            Nuevo Servicio
        </h1>

        <form @submit.prevent="submit" class="flex max-w-lg flex-col gap-4">
            <FormField label="Motocicleta" :error="form.errors.motocicleta_id">
                <select v-model="form.motocicleta_id" :class="inputClasses">
                    <option value="">
                        -- Selecciona una motocicleta --
                    </option>
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
                label="Mecánico (opcional)"
                :error="form.errors.mecanico_id"
            >
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

            <div class="mt-2 flex gap-3">
                <AppButton
                    type="submit"
                    variant="primary"
                    :disabled="form.processing"
                >
                    Guardar
                </AppButton>
                <AppButton href="/servicios">Cancelar</AppButton>
            </div>
        </form>
    </AppLayout>
</template>
