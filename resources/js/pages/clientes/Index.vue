<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

import AppButton from '../../components/AppButton.vue';
import ConfirmDialog from '../../components/ConfirmDialog.vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    clientes: Object,
    filtros: Object,
});

const buscar = ref(props.filtros.buscar || '');
const clienteAEliminar = ref(null);

function buscarClientes() {
    router.get(
        '/clientes',
        { buscar: buscar.value },
        { preserveState: true, replace: true },
    );
}

function confirmarEliminar(cliente) {
    clienteAEliminar.value = cliente;
}

function eliminarCliente() {
    router.delete(`/clientes/${clienteAEliminar.value.id}`, {
        onFinish: () => (clienteAEliminar.value = null),
    });
}

function iniciales(cliente) {
    return `${cliente.nombre?.[0] ?? ''}${cliente.apellido?.[0] ?? ''}`.toUpperCase();
}

function etiquetaPagina(link, index) {
    if (index === 0) return 'Anterior';
    if (index === props.clientes.links.length - 1) return 'Siguiente';
    return link.label;
}
</script>

<template>
    <Head title="Clientes" />

    <AppLayout>
        <!-- Encabezado -->
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-slate-50">Clientes</h1>
                <p class="mt-1 text-sm text-slate-400">
                    Administra los clientes registrados en el taller.
                </p>
            </div>
            <AppButton href="/clientes/create" variant="primary">
                + Nuevo Cliente
            </AppButton>
        </div>

        <!-- Buscador -->
        <form @submit.prevent="buscarClientes" class="mb-6 flex gap-2">
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
                    placeholder="Buscar por nombre o documento"
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
                            <th>Cliente</th>
                            <th>Documento</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="cliente in clientes.data" :key="cliente.id">
                            <td>
                                <div class="flex items-center gap-3">
                                    <span
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-500/15 text-xs font-semibold text-amber-400"
                                    >
                                        {{ iniciales(cliente) }}
                                    </span>
                                    <span class="font-medium text-slate-50">
                                        {{ cliente.nombre }}
                                        {{ cliente.apellido }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-slate-300">
                                {{ cliente.documento }}
                            </td>
                            <td class="text-slate-300">
                                {{ cliente.telefono }}
                            </td>
                            <td class="text-slate-400">{{ cliente.email }}</td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <AppButton
                                        :href="`/clientes/${cliente.id}/edit`"
                                        size="sm"
                                    >
                                        Editar
                                    </AppButton>
                                    <AppButton
                                        size="sm"
                                        variant="danger"
                                        @click="confirmarEliminar(cliente)"
                                    >
                                        Eliminar
                                    </AppButton>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="clientes.data.length === 0">
                            <td
                                colspan="5"
                                class="py-12 text-center text-slate-400"
                            >
                                No hay clientes registrados.
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
                    <template v-if="clientes.total > 0">
                        Mostrando {{ clientes.from }} a {{ clientes.to }} de
                        {{ clientes.total }} clientes
                    </template>
                    <template v-else>Sin resultados</template>
                </p>

                <div v-if="clientes.links.length > 3" class="flex gap-1">
                    <template
                        v-for="(link, index) in clientes.links"
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
            :show="!!clienteAEliminar"
            title="Eliminar cliente"
            :message="`¿Eliminar a ${clienteAEliminar?.nombre} ${clienteAEliminar?.apellido}?`"
            @confirm="eliminarCliente"
            @cancel="clienteAEliminar = null"
        />
    </AppLayout>
</template>
