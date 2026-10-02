<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

import AppBadge from '../../components/AppBadge.vue';
import AppButton from '../../components/AppButton.vue';
import ConfirmDialog from '../../components/ConfirmDialog.vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    servicios: Object,
    filtros: Object,
});

const buscar = ref(props.filtros.buscar || '');
const servicioAEliminar = ref(null);

function buscarServicios() {
    router.get(
        '/servicios',
        { buscar: buscar.value },
        { preserveState: true, replace: true },
    );
}

function confirmarEliminar(servicio) {
    servicioAEliminar.value = servicio;
}

function eliminarServicio() {
    router.delete(`/servicios/${servicioAEliminar.value.id}`, {
        onFinish: () => (servicioAEliminar.value = null),
    });
}

const estadoLabels = {
    pendiente: 'Pendiente',
    en_proceso: 'En proceso',
    terminado: 'Terminado',
    entregado: 'Entregado',
};

const estadoVariants = {
    pendiente: 'neutral',
    en_proceso: 'warning',
    terminado: 'info',
    entregado: 'ok',
};

function etiquetaPagina(link, index) {
    if (index === 0) return 'Anterior';
    if (index === props.servicios.links.length - 1) return 'Siguiente';
    return link.label;
}
</script>

<template>
    <Head title="Servicios" />

    <AppLayout>
        <!-- Encabezado -->
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-slate-50">Servicios</h1>
                <p class="mt-1 text-sm text-slate-400">
                    Órdenes de trabajo del taller y su estado actual.
                </p>
            </div>
            <AppButton href="/servicios/create" variant="primary">
                + Nuevo Servicio
            </AppButton>
        </div>

        <!-- Buscador -->
        <form @submit.prevent="buscarServicios" class="mb-6 flex gap-2">
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
                    placeholder="Buscar por placa"
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
                            <th>Moto</th>
                            <th>Cliente</th>
                            <th>Mecánico</th>
                            <th>Estado</th>
                            <th>Costo total</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="servicio in servicios.data"
                            :key="servicio.id"
                        >
                            <td>
                                <span
                                    class="rounded-md border border-amber-500/40 bg-amber-500/10 px-2.5 py-1 font-mono text-xs font-semibold tracking-widest text-amber-400 uppercase"
                                >
                                    {{ servicio.motocicleta.placa }}
                                </span>
                            </td>
                            <td class="font-medium text-slate-50">
                                {{ servicio.motocicleta.cliente.nombre }}
                                {{ servicio.motocicleta.cliente.apellido }}
                            </td>
                            <td
                                :class="
                                    servicio.mecanico
                                        ? 'text-slate-300'
                                        : 'text-slate-500 italic'
                                "
                            >
                                {{ servicio.mecanico?.name ?? 'Sin asignar' }}
                            </td>
                            <td>
                                <AppBadge
                                    :variant="estadoVariants[servicio.estado]"
                                >
                                    {{ estadoLabels[servicio.estado] }}
                                </AppBadge>
                            </td>
                            <td
                                class="font-semibold text-slate-100 tabular-nums"
                            >
                                ${{
                                    Number(servicio.costo_total).toLocaleString(
                                        'es-CO',
                                    )
                                }}
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <AppButton
                                        :href="`/servicios/${servicio.id}/edit`"
                                        size="sm"
                                    >
                                        Editar
                                    </AppButton>
                                    <AppButton
                                        size="sm"
                                        variant="danger"
                                        @click="confirmarEliminar(servicio)"
                                    >
                                        Eliminar
                                    </AppButton>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="servicios.data.length === 0">
                            <td
                                colspan="6"
                                class="py-12 text-center text-slate-400"
                            >
                                No hay servicios registrados.
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
                    <template v-if="servicios.total > 0">
                        Mostrando {{ servicios.from }} a {{ servicios.to }} de
                        {{ servicios.total }} servicios
                    </template>
                    <template v-else>Sin resultados</template>
                </p>

                <div v-if="servicios.links.length > 3" class="flex gap-1">
                    <template
                        v-for="(link, index) in servicios.links"
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
            :show="!!servicioAEliminar"
            title="Eliminar servicio"
            :message="`¿Eliminar el servicio de la moto ${servicioAEliminar?.motocicleta?.placa}?`"
            @confirm="eliminarServicio"
            @cancel="servicioAEliminar = null"
        />
    </AppLayout>
</template>
