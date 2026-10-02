<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

import AppButton from '../../components/AppButton.vue';
import ConfirmDialog from '../../components/ConfirmDialog.vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    motocicletas: Object,
    filtros: Object,
});

const buscar = ref(props.filtros.buscar || '');
const motoAEliminar = ref(null);

function buscarMotos() {
    router.get(
        '/motocicletas',
        { buscar: buscar.value },
        { preserveState: true, replace: true },
    );
}

function confirmarEliminar(moto) {
    motoAEliminar.value = moto;
}

function eliminarMoto() {
    router.delete(`/motocicletas/${motoAEliminar.value.id}`, {
        onFinish: () => (motoAEliminar.value = null),
    });
}

function etiquetaPagina(link, index) {
    if (index === 0) return 'Anterior';
    if (index === props.motocicletas.links.length - 1) return 'Siguiente';
    return link.label;
}
</script>

<template>
    <Head title="Motocicletas" />

    <AppLayout>
        <!-- Encabezado -->
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-slate-50">
                    Motocicletas
                </h1>
                <p class="mt-1 text-sm text-slate-400">
                    Motocicletas de los clientes que ingresan al taller.
                </p>
            </div>
            <AppButton href="/motocicletas/create" variant="primary">
                + Nueva Motocicleta
            </AppButton>
        </div>

        <!-- Buscador -->
        <form @submit.prevent="buscarMotos" class="mb-6 flex gap-2">
            <div class="relative w-full max-w-sm">
                <svg
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-slate-500"
                    viewBox="0 0 20 20"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                >
                    <circle cx="9" cy="9" r="6" />
                    <path d="M14 14l4 4" />
                </svg>
                <input
                    type="text"
                    v-model="buscar"
                    placeholder="Buscar por placa, marca o modelo"
                    class="campo pl-10"
                />
            </div>
            <AppButton type="submit">Buscar</AppButton>
        </form>

        <!-- Tarjeta con tabla -->
        <div class="panel">
            <div class="overflow-x-auto">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Placa</th>
                            <th>Motocicleta</th>
                            <th>Dueño</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="moto in motocicletas.data" :key="moto.id">
                            <td>
                                <span
                                    class="rounded-md border border-amber-500/40 bg-amber-500/10 px-2.5 py-1 font-mono text-xs font-semibold tracking-widest text-amber-400 uppercase"
                                >
                                    {{ moto.placa }}
                                </span>
                            </td>
                            <td>
                                <span class="font-medium text-slate-50">
                                    {{ moto.marca }}
                                </span>
                                <span class="ml-1 text-slate-400">
                                    {{ moto.modelo }}
                                </span>
                            </td>
                            <td class="text-slate-300">
                                {{ moto.cliente.nombre }}
                                {{ moto.cliente.apellido }}
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <AppButton
                                        :href="`/motocicletas/${moto.id}/edit`"
                                        size="sm"
                                    >
                                        Editar
                                    </AppButton>
                                    <AppButton
                                        size="sm"
                                        variant="danger"
                                        @click="confirmarEliminar(moto)"
                                    >
                                        Eliminar
                                    </AppButton>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="motocicletas.data.length === 0">
                            <td
                                colspan="4"
                                class="py-12 text-center text-slate-400"
                            >
                                No hay motocicletas registradas.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pie: conteo + paginación -->
            <div
                class="border-linea flex flex-col gap-3 border-t px-4 py-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <p class="text-sm text-slate-400">
                    <template v-if="motocicletas.total > 0">
                        Mostrando {{ motocicletas.from }} a
                        {{ motocicletas.to }} de {{ motocicletas.total }}
                        motocicletas
                    </template>
                    <template v-else>Sin resultados</template>
                </p>

                <div v-if="motocicletas.links.length > 3" class="flex gap-1">
                    <template
                        v-for="(link, index) in motocicletas.links"
                        :key="index"
                    >
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="min-w-9 rounded-md border px-3 py-1.5 text-center text-sm transition-colors"
                            :class="
                                link.active
                                    ? 'border-amber-500 bg-amber-500 font-semibold text-slate-900'
                                    : 'border-linea hover:bg-superficie-alta text-slate-300'
                            "
                        >
                            {{ etiquetaPagina(link, index) }}
                        </Link>
                        <span
                            v-else
                            class="border-linea min-w-9 rounded-md border px-3 py-1.5 text-center text-sm text-slate-600"
                        >
                            {{ etiquetaPagina(link, index) }}
                        </span>
                    </template>
                </div>
            </div>
        </div>

        <ConfirmDialog
            :show="!!motoAEliminar"
            title="Eliminar motocicleta"
            :message="`¿Eliminar la moto ${motoAEliminar?.placa}?`"
            @confirm="eliminarMoto"
            @cancel="motoAEliminar = null"
        />
    </AppLayout>
</template>
