<script setup>
import { Head } from '@inertiajs/vue3';

import AppBadge from '../../components/AppBadge.vue';
import AppButton from '../../components/AppButton.vue';
import AppLayout from '../../layouts/AppLayout.vue';

defineProps({
    facturas: { type: Array, default: () => [] },
});

function badgeVariant(estado) {
    if (estado === 'pagada') return 'ok';
    if (estado === 'anulada') return 'danger';
    return 'warning';
}

function dinero(valor) {
    return `$${Number(valor).toLocaleString('es-CO')}`;
}
</script>

<template>
    <Head title="Mis facturas" />

    <AppLayout>
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-slate-50">
                    Mis facturas
                </h1>
                <p class="mt-1 text-sm text-slate-400">
                    Facturas de los servicios realizados a tus motocicletas.
                </p>
            </div>
        </div>

        <div class="panel">
            <div class="overflow-x-auto">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Motocicleta</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th class="text-right">PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="factura in facturas" :key="factura.id">
                            <td
                                class="font-mono text-xs font-semibold tracking-wider text-amber-400"
                            >
                                {{ factura.numero_factura }}
                            </td>
                            <td>
                                <span
                                    class="rounded-md border border-amber-500/40 bg-amber-500/10 px-2.5 py-1 font-mono text-xs font-semibold tracking-widest text-amber-400 uppercase"
                                >
                                    {{ factura.servicio?.motocicleta?.placa }}
                                </span>
                            </td>
                            <td
                                class="font-semibold text-slate-100 tabular-nums"
                            >
                                {{ dinero(factura.total) }}
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
                                <div class="flex justify-end">
                                    <a
                                        :href="`/portal/facturas/${factura.id}/pdf`"
                                        target="_blank"
                                        rel="noopener"
                                        class="border-linea hover:bg-superficie-alta rounded-md border px-3 py-1.5 text-sm text-slate-200 transition-colors"
                                    >
                                        PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="facturas.length === 0">
                            <td
                                colspan="5"
                                class="py-12 text-center text-slate-400"
                            >
                                Todavía no tienes facturas.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
