<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

import AppBadge from '../../components/AppBadge.vue';
import AppButton from '../../components/AppButton.vue';
import ConfirmDialog from '../../components/ConfirmDialog.vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    facturas: Object,
});

const facturaAEliminar = ref(null);

function confirmarEliminar(factura) {
    facturaAEliminar.value = factura;
}

function eliminarFactura() {
    router.delete(`/facturas/${facturaAEliminar.value.id}`, {
        onFinish: () => (facturaAEliminar.value = null),
    });
}

function badgeVariant(estado) {
    if (estado === 'pagada') return 'ok';
    if (estado === 'anulada') return 'danger';
    return 'warning';
}

function etiquetaPagina(link, index) {
    if (index === 0) return 'Anterior';
    if (index === props.facturas.links.length - 1) return 'Siguiente';
    return link.label;
}
</script>

<template>
    <Head title="Facturas" />

    <AppLayout>
        <!-- Encabezado -->
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-slate-50">Facturas</h1>
                <p class="mt-1 text-sm text-slate-400">
                    Facturación de los servicios realizados en el taller.
                </p>
            </div>
            <AppButton href="/facturas/create" variant="primary">
                + Nueva Factura
            </AppButton>
        </div>

        <!-- Tarjeta con tabla -->
        <div class="panel">
            <div class="overflow-x-auto">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Cliente</th>
                            <th>Motocicleta</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="factura in facturas.data" :key="factura.id">
                            <td
                                class="font-mono text-xs font-semibold tracking-wider text-amber-400"
                            >
                                {{ factura.numero_factura }}
                            </td>
                            <td class="font-medium text-slate-50">
                                {{ factura.servicio.motocicleta.cliente.nombre }}
                                {{
                                    factura.servicio.motocicleta.cliente
                                        .apellido
                                }}
                            </td>
                            <td>
                                <span
                                    class="rounded-md border border-amber-500/40 bg-amber-500/10 px-2.5 py-1 font-mono text-xs font-semibold tracking-widest text-amber-400 uppercase"
                                >
                                    {{ factura.servicio.motocicleta.placa }}
                                </span>
                            </td>
                            <td class="font-semibold text-slate-100 tabular-nums">
                                ${{
                                    Number(factura.total).toLocaleString('es-CO')
                                }}
                            </td>
                            <td>
                                <AppBadge
                                    :variant="badgeVariant(factura.estado)"
                                >
                                    <span class="capitalize">
                                        {{ factura.estado }}
                                    </span>
                                </AppBadge>
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a
                                        :href="`/facturas/${factura.id}/pdf`"
                                        target="_blank"
                                        class="inline-flex items-center justify-center rounded-md border border-linea px-3 py-1.5 text-xs text-slate-100 transition-colors hover:bg-superficie-alta"
                                    >
                                        PDF
                                    </a>
                                    <AppButton
                                        :href="`/facturas/${factura.id}/edit`"
                                        size="sm"
                                    >
                                        Editar
                                    </AppButton>
                                    <AppButton
                                        size="sm"
                                        variant="danger"
                                        @click="confirmarEliminar(factura)"
                                    >
                                        Eliminar
                                    </AppButton>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="facturas.data.length === 0">
                            <td
                                colspan="6"
                                class="py-12 text-center text-slate-400"
                            >
                                No hay facturas registradas.
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
                    <template v-if="facturas.total > 0">
                        Mostrando {{ facturas.from }} a {{ facturas.to }} de
                        {{ facturas.total }} facturas
                    </template>
                    <template v-else>Sin resultados</template>
                </p>

                <div v-if="facturas.links.length > 3" class="flex gap-1">
                    <template
                        v-for="(link, index) in facturas.links"
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
            :show="!!facturaAEliminar"
            title="Eliminar factura"
            :message="`¿Eliminar la factura ${facturaAEliminar?.numero_factura}?`"
            @confirm="eliminarFactura"
            @cancel="facturaAEliminar = null"
        />
    </AppLayout>
</template>
