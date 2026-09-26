<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    repuestos: Object,
    filtros: Object,
});

const buscar = ref(props.filtros.buscar || '');

function buscarRepuestos() {
    router.get('/repuestos', { buscar: buscar.value }, {
        preserveState: true,
        replace: true,
    });
}

function eliminarRepuesto(repuesto) {
    if (confirm(`¿Eliminar el repuesto ${repuesto.nombre}?`)) {
        router.delete(`/repuestos/${repuesto.id}`);
    }
}
</script>

<template>
    <Head title="Repuestos" />

    <AppLayout>
        <div class="header-row">
            <h1>Repuestos</h1>
            <Link href="/repuestos/create" class="btn btn-primary">+ Nuevo Repuesto</Link>
        </div>

        <form @submit.prevent="buscarRepuestos" class="search-row">
            <input type="text" v-model="buscar" placeholder="Buscar por nombre" />
            <button type="submit" class="btn">Buscar</button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="repuesto in repuestos.data" :key="repuesto.id">
                    <td>{{ repuesto.nombre }}</td>
                    <td>${{ Number(repuesto.precio).toLocaleString('es-CO') }}</td>
                    <td>{{ repuesto.cantidad }}</td>
                    <td>
                        <span
                            class="badge"
                            :class="repuesto.cantidad <= repuesto.cantidad_minima ? 'badge-danger' : 'badge-ok'"
                        >
                            {{ repuesto.cantidad <= repuesto.cantidad_minima ? 'Bajo stock' : 'OK' }}
                        </span>
                    </td>
                    <td class="actions">
                        <Link :href="`/repuestos/${repuesto.id}/edit`" class="btn btn-sm">Editar</Link>
                        <button class="btn btn-sm btn-danger" @click="eliminarRepuesto(repuesto)">Eliminar</button>
                    </td>
                </tr>
                <tr v-if="repuestos.data.length === 0">
                    <td colspan="5" class="empty">No hay repuestos registrados.</td>
                </tr>
            </tbody>
        </table>

        <div class="pagination">
            <template v-for="link in repuestos.links" :key="link.label">
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
.badge-ok { background: rgba(34, 197, 94, 0.15); color: #4ade80; }
.badge-danger { background: rgba(185, 28, 28, 0.15); color: #fca5a5; }
</style>
