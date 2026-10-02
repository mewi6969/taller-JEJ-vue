<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

import AppButton from '../../components/AppButton.vue';
import FormField from '../../components/FormField.vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    existentes: { type: Array, default: () => [] },
});

const form = useForm({
    nombre: '',
    descripcion: '',
    precio: '',
    cantidad: '',
    cantidad_minima: '',
});

// Repuesto que ya existe con el nombre escrito (sin importar mayúsculas ni espacios)
const existente = computed(() => {
    const buscado = form.nombre.trim().toLowerCase();

    if (buscado === '') {
        return null;
    }

    return (
        props.existentes.find(
            (repuesto) => repuesto.nombre.trim().toLowerCase() === buscado,
        ) ?? null
    );
});

const aviso = computed(() => {
    if (!existente.value) {
        return undefined;
    }

    return `Ya existe con ${existente.value.cantidad} unidades. Al guardar se sumará la cantidad que indiques a ese stock; la descripción y el precio del repuesto existente no cambian.`;
});

// Al elegir un repuesto existente se rellenan sus datos;
// si luego se cambia el nombre, se limpian para no dejar datos ajenos.
const autocompletado = ref(false);

watch(existente, (repuesto) => {
    if (repuesto) {
        form.descripcion = repuesto.descripcion ?? '';
        form.precio = repuesto.precio;
        form.cantidad_minima = repuesto.cantidad_minima;
        autocompletado.value = true;
    } else if (autocompletado.value) {
        form.descripcion = '';
        form.precio = '';
        form.cantidad_minima = '';
        autocompletado.value = false;
    }
});

function submit() {
    form.post('/repuestos');
}
</script>

<template>
    <Head title="Nuevo Repuesto" />

    <AppLayout>
        <div class="mx-auto max-w-2xl">
            <!-- Encabezado -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold text-slate-50">
                    Nuevo Repuesto
                </h1>
                <p class="mt-1 text-sm text-slate-400">
                    Agrega un repuesto al inventario del taller.
                </p>
            </div>

            <!-- Tarjeta del formulario -->
            <form @submit.prevent="submit" class="panel">
                <div class="grid gap-5 p-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <FormField
                            label="Nombre"
                            :error="form.errors.nombre"
                            :ayuda="aviso"
                        >
                            <input
                                type="text"
                                v-model="form.nombre"
                                list="lista-repuestos"
                                autocomplete="off"
                                class="campo"
                            />
                            <datalist id="lista-repuestos">
                                <option
                                    v-for="repuesto in existentes"
                                    :key="repuesto.id"
                                    :value="repuesto.nombre"
                                />
                            </datalist>
                        </FormField>
                    </div>

                    <div class="sm:col-span-2">
                        <FormField
                            label="Descripción"
                            :error="form.errors.descripcion"
                        >
                            <input
                                type="text"
                                v-model="form.descripcion"
                                class="campo"
                            />
                        </FormField>
                    </div>

                    <div class="sm:col-span-2">
                        <FormField label="Precio" :error="form.errors.precio">
                            <input
                                type="number"
                                step="0.01"
                                v-model="form.precio"
                                class="campo"
                            />
                        </FormField>
                    </div>

                    <FormField label="Cantidad" :error="form.errors.cantidad">
                        <input
                            type="number"
                            v-model="form.cantidad"
                            class="campo"
                        />
                    </FormField>

                    <FormField
                        label="Cantidad mínima"
                        :error="form.errors.cantidad_minima"
                        ayuda="Si la cantidad llega a este número o menos, se marca como bajo stock."
                    >
                        <input
                            type="number"
                            v-model="form.cantidad_minima"
                            class="campo"
                        />
                    </FormField>
                </div>

                <!-- Pie con botones -->
                <div
                    class="border-linea bg-superficie-alta/40 flex justify-end gap-3 border-t px-6 py-4"
                >
                    <AppButton href="/repuestos">Cancelar</AppButton>
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
