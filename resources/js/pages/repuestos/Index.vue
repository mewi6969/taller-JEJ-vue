<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

import AppBadge from '../../components/AppBadge.vue';
import AppButton from '../../components/AppButton.vue';
import ConfirmDialog from '../../components/ConfirmDialog.vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    repuestos: Object,
    filtros: Object,
});

const buscar = ref(props.filtros.buscar || '');
const repuestoAEliminar = ref(null);

function buscarRepuestos() {
    router.get(
        '/repuestos',
        { buscar: buscar.value },
        { preserveState: true, replace: true },
    );
}

function confirmarEliminar(repuesto) {
    repuestoAEliminar.value = repuesto;
}

function eliminarRepuesto() {
    router.delete(`/repuestos/${repuestoAEliminar.value.id}`, {
        onFinish: () => (repuestoAEliminar.value = null),
    });
}

function bajoStock(repuesto) {
    return repuesto.cantidad <= repuesto.cantidad_minima;
}

function etiquetaPagina(link, index) {
    if (index === 0) return 'Anterior';
    if (index === props.repuestos.links.length - 1) return 'Siguiente';
    return link.label;
}
</script>

<template>
    <Head title="Repuestos" />

    <AppLayout>
        <!-- Encabezado -->
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-slate-50">Repuestos</h1>
                <p class="mt-1 text-sm text-slate-400">
                    Inventario de repuestos y control de existencias.
                </p>
            </div>
            <AppButton href="/repuestos/create" variant="primary">
                + Nuevo Repuesto
            </AppButton>
        </div>

        <!-- Buscador -->
        <form @submit.prevent="buscarRepuestos" class="mb-6 flex gap-2">
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
                    placeholder="Buscar por nombre"
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
                            <th>Repuesto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="repuesto in repuestos.data" :key="repuesto.id">
                            <td class="font-medium text-slate-50">
                                {{ repuesto.nombre }}
                            </td>
                            <td class="text-slate-300 tabular-nums">
                                ${{
                                    Number(repuesto.precio).toLocaleString(
                                        'es-CO',
                                    )
                                }}
                            </td>
                            <td
                                class="font-semibold tabular-nums"
                                :class="
                                    bajoStock(repuesto)
                                        ? 'text-red-400'
                                        : 'text-slate-200'
                                "
                            >
                                {{ repuesto.cantidad }}
                            </td>
                            <td>
                                <AppBadge
                                    :variant="
                                        bajoStock(repuesto) ? 'danger' : 'ok'
                                    "
                                >
                                    {{ bajoStock(repuesto) ? 'Bajo stock' : 'OK' }}
                                </AppBadge>
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <AppButton
                                        :href="`/repuestos/${repuesto.id}/edit`"
                                        size="sm"
                                    >
                                        Editar
                                    </AppButton>
                                    <AppButton
                                        size="sm"
                                        variant="danger"
                                        @click="confirmarEliminar(repuesto)"
                                    >
                                        Eliminar
                                    </AppButton>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="repuestos.data.length === 0">
                            <td
                                colspan="5"
                                class="py-12 text-center text-slate-400"
                            >
                                No hay repuestos registrados.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pie: conteo + paginación -->
            <div
                class="flex flex-col gap-3 border-t border-linea px-4 py-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <p class="text-sm text-slate-400">
                    <template v-if="repuestos.total > 0">
                        Mostrando {{ repuestos.from }} a {{ repuestos.to }} de
                        {{ repuestos.total }} repuestos
                    </template>
                    <template v-else>Sin resultados</template>
                </p>

                <div v-if="repuestos.links.length > 3" class="flex gap-1">
                    <template
                        v-for="(link, index) in repuestos.links"
                        :key="index"
                    >
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="min-w-9 rounded-md border px-3 py-1.5 text-center text-sm transition-colors"
                            :class="
                                link.active
                                    ? 'border-amber-500 bg-amber-500 font-semibold text-slate-900'
                                    : 'border-linea text-slate-300 hover:bg-superficie-alta'
                            "
                        >
                            {{ etiquetaPagina(link, index) }}
                        </Link>
                        <span
                            v-else
                            class="min-w-9 rounded-md border border-linea px-3 py-1.5 text-center text-sm text-slate-600"
                        >
                            {{ etiquetaPagina(link, index) }}
                        </span>
                    </template>
                </div>
            </div>
        </div>

        <ConfirmDialog
            :show="!!repuestoAEliminar"
            title="Eliminar repuesto"
            :message="`¿Eliminar el repuesto ${repuestoAEliminar?.nombre}?`"
            @confirm="eliminarRepuesto"
            @cancel="repuestoAEliminar = null"
        />
    </AppLayout>
</template>
