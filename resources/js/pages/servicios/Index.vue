<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    servicios: Object,
    filtros: Object,
});

const buscar = ref(props.filtros.buscar || '');

function buscarServicios() {
    router.get('/servicios', { buscar: buscar.value }, {
        preserveState: true,
        replace: true,
    });
}

function eliminarServicio(servicio) {
    if (confirm(`¿Eliminar el servicio de la moto ${servicio.motocicleta.placa}?`)) {
        router.delete(`/servicios/${servicio.id}`);
    }
}

const estadoLabels = {
    pendiente: 'Pendiente',
    en_proceso: 'En proceso',
    terminado: 'Terminado',
    entregado: 'Entregado',
};

const estadoClases = {
    pendiente: 'badge-pendiente',
    en_proceso: 'badge-proceso',
    terminado: 'badge-terminado',
    entregado: 'badge-entregado',
};
</script>

<template>
    <Head title="Servicios" />

    <AppLayout>
        <div class="header-row">
            <h1>Servicios</h1>
            <Link href="/servicios/create" class="btn btn-primary">+ Nuevo Servicio</Link>
        </div>

        <form @submit.prevent="buscarServicios" class="search-row">
            <input type="text" v-model="buscar" placeholder="Buscar por placa" />
            <button type="submit" class="btn">Buscar</button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Moto</th>
                    <th>Cliente</th>
                    <th>Mecánico</th>
                    <th>Estado</th>
                    <th>Costo total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="servicio in servicios.data" :key="servicio.id">
                    <td>{{ servicio.motocicleta.placa }}</td>
                    <td>{{ servicio.motocicleta.cliente.nombre }} {{ servicio.motocicleta.cliente.apellido }}</td>
                    <td>{{ servicio.mecanico?.name ?? 'Sin asignar' }}</td>
                    <td>
                        <span class="badge" :class="estadoClases[servicio.estado]">
                            {{ estadoLabels[servicio.estado] }}
                        </span>
                    </td>
                    <td>${{ Number(servicio.costo_total).toLocaleString('es-CO') }}</td>
                    <td class="actions">
                        <Link :href="`/servicios/${servicio.id}/edit`" class="btn btn-sm">Editar</Link>
                        <button class="btn btn-sm btn-danger" @click="eliminarServicio(servicio)">Eliminar</button>
                    </td>
                </tr>
                <tr v-if="servicios.data.length === 0">
                    <td colspan="6" class="empty">No hay servicios registrados.</td>
                </tr>
            </tbody>
        </table>

        <div class="pagination">
            <template v-for="link in servicios.links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="page-link"
                    :class="{ active: link.active }"
                    v-html="link.label"
                />
                <span v-else class="page-link disabled" v-html="link.label" />
            </template>
        </div>
    </AppLayout>
</template>

<style scoped>
.header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
.search-row { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; }
.search-row input { flex: 1; max-width: 320px; padding: 0.5rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #e2e8f0; }
.table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
.table th, .table td { padding: 0.6rem; border-bottom: 1px solid #334155; text-align: left; }
.actions { display: flex; gap: 0.5rem; }
.empty { text-align: center; color: #94a3b8; }
.btn { display: inline-block; padding: 0.5rem 0.9rem; border-radius: 6px; border: 1px solid #475569; background: transparent; color: #e2e8f0; cursor: pointer; text-decoration: none; font-size: 0.9rem; }
.btn-primary { background: #3b82f6; border-color: #3b82f6; color: white; }
.btn-sm { padding: 0.3rem 0.6rem; font-size: 0.8rem; }
.btn-danger { border-color: #b91c1c; color: #fca5a5; }
.pagination { display: flex; gap: 0.4rem; flex-wrap: wrap; }
.page-link { padding: 0.35rem 0.7rem; border-radius: 6px; border: 1px solid #334155; color: #e2e8f0; text-decoration: none; font-size: 0.85rem; }
.page-link.active { background: #3b82f6; border-color: #3b82f6; }
.page-link.disabled { opacity: 0.4; }
.badge { padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
.badge-pendiente { background: rgba(148, 163, 184, 0.15); color: #cbd5e1; }
.badge-proceso { background: rgba(234, 179, 8, 0.15); color: #fde047; }
.badge-terminado { background: rgba(59, 130, 246, 0.15); color: #93c5fd; }
.badge-entregado { background: rgba(34, 197, 94, 0.15); color: #4ade80; }
</style>
