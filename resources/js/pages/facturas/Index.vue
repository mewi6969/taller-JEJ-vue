<script setup>
import { Head, Link, router } from '@inertiajs/vue3';

import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    facturas: Object,
});

function eliminarFactura(factura) {
    if (confirm(`¿Eliminar la factura ${factura.numero_factura}?`)) {
        router.delete(`/facturas/${factura.id}`);
    }
}

function badgeClass(estado) {
    if (estado === 'pagada') return 'badge-ok';
    if (estado === 'anulada') return 'badge-danger';
    return 'badge-pendiente';
}
</script>

<template>
    <Head title="Facturas" />

    <AppLayout>
        <div class="header-row">
            <h1>Facturas</h1>
            <Link href="/facturas/create" class="btn btn-primary"
                >+ Nueva Factura</Link
            >
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Cliente</th>
                    <th>Motocicleta</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="factura in facturas.data" :key="factura.id">
                    <td>{{ factura.numero_factura }}</td>
                    <td>
                        {{ factura.servicio.motocicleta.cliente.nombre }}
                        {{ factura.servicio.motocicleta.cliente.apellido }}
                    </td>
                    <td>{{ factura.servicio.motocicleta.placa }}</td>
                    <td>
                        ${{ Number(factura.total).toLocaleString('es-CO') }}
                    </td>
                    <td>
                        <span class="badge" :class="badgeClass(factura.estado)">
                            {{ factura.estado }}
                        </span>
                    </td>
                    <td class="actions">
                        <a
                            :href="`/facturas/${factura.id}/pdf`"
                            class="btn btn-sm"
                            target="_blank"
                            >PDF</a
                        >
                        <Link
                            :href="`/facturas/${factura.id}/edit`"
                            class="btn btn-sm"
                            >Editar</Link
                        >
                        <button
                            class="btn btn-sm btn-danger"
                            @click="eliminarFactura(factura)"
                        >
                            Eliminar
                        </button>
                    </td>
                </tr>
                <tr v-if="facturas.data.length === 0">
                    <td colspan="6" class="empty">
                        No hay facturas registradas.
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="pagination">
            <template v-for="link in facturas.links" :key="link.label">
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
.header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}
.table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1.5rem;
}
.table th,
.table td {
    padding: 0.6rem;
    border-bottom: 1px solid #334155;
    text-align: left;
}
.actions {
    display: flex;
    gap: 0.5rem;
}
.empty {
    text-align: center;
    color: #94a3b8;
}
.btn {
    display: inline-block;
    padding: 0.5rem 0.9rem;
    border-radius: 6px;
    border: 1px solid #475569;
    background: transparent;
    color: #e2e8f0;
    cursor: pointer;
    text-decoration: none;
    font-size: 0.9rem;
}
.btn-primary {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
}
.btn-sm {
    padding: 0.3rem 0.6rem;
    font-size: 0.8rem;
}
.btn-danger {
    border-color: #b91c1c;
    color: #fca5a5;
}
.pagination {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
}
.page-link {
    padding: 0.35rem 0.7rem;
    border-radius: 6px;
    border: 1px solid #334155;
    color: #e2e8f0;
    text-decoration: none;
    font-size: 0.85rem;
}
.page-link.active {
    background: #3b82f6;
    border-color: #3b82f6;
}
.page-link.disabled {
    opacity: 0.4;
}
.badge {
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: capitalize;
}
.badge-ok {
    background: rgba(34, 197, 94, 0.15);
    color: #4ade80;
}
.badge-danger {
    background: rgba(185, 28, 28, 0.15);
    color: #fca5a5;
}
.badge-pendiente {
    background: rgba(234, 179, 8, 0.15);
    color: #fbbf24;
}
</style>
