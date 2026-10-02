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
</script>

<template>
    <Head title="Nuevo Servicio" />

    <AppLayout>
        <div class="mx-auto max-w-2xl">
            <!-- Encabezado -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold text-slate-50">
                    Nuevo Servicio
                </h1>
                <p class="mt-1 text-sm text-slate-400">
                    Registra el ingreso de una motocicleta al taller. Los
                    repuestos se agregan después, al editar el servicio.
                </p>
            </div>

            <!-- Tarjeta del formulario -->
            <form @submit.prevent="submit" class="panel">
                <div class="grid gap-5 p-6 sm:grid-cols-2">
                    <FormField
                        label="Motocicleta"
                        :error="form.errors.motocicleta_id"
                    >
                        <select v-model="form.motocicleta_id" class="campo">
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
                        {{ form.processing ? 'Guardando...' : 'Guardar' }}
                    </AppButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
