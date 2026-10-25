<script setup>
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import AppBadge from '../../components/AppBadge.vue';
import AppButton from '../../components/AppButton.vue';
import ConfirmDialog from '../../components/ConfirmDialog.vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    pedidos: Array,
});

const pedidoACancelar = ref(null);

const filas = computed(() =>
    props.pedidos.map((pedido) => ({
        ...pedido,
        total: pedido.detalles.reduce(
            (suma, d) => suma + d.cantidad * Number(d.precio_unitario),
            0,
        ),
    })),
);

function badgeVariant(estado) {
    if (estado === 'atendido') return 'ok';
    if (estado === 'cancelado') return 'danger';
    return 'warning';
}

function fecha(valor) {
    return new Date(valor).toLocaleDateString('es-CO', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

function cambiarEstado(pedido, estado, alTerminar) {
    router.patch(
        `/pedidos/${pedido.id}`,
        { estado },
        { preserveScroll: true, onFinish: alTerminar },
    );
}

function atender(pedido) {
    cambiarEstado(pedido, 'atendido');
}

function cancelarPedido() {
    cambiarEstado(pedidoACancelar.value, 'cancelado', () => {
        pedidoACancelar.value = null;
    });
}
</script>

<template>
    <Head title="Pedidos" />

    <AppLayout>
        <!-- Encabezado -->
        <div class="mb-8">
            <h1 class="text-3xl font-semibold text-slate-50">Pedidos</h1>
            <p class="mt-1 text-sm text-slate-400">
                Pedidos de repuestos hechos por los clientes desde el portal.
            </p>
        </div>

        <!-- Tarjeta con tabla -->
        <div class="panel">
            <div class="overflow-x-auto">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Repuestos</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="pedido in filas" :key="pedido.id">
                            <td class="text-slate-300">
                                {{ fecha(pedido.created_at) }}
                            </td>
                            <td class="font-medium text-slate-50">
                                <template v-if="pedido.cliente">
                                    {{ pedido.cliente.nombre }}
                                    {{ pedido.cliente.apellido }}
                                    <div
                                        class="text-xs font-normal text-slate-400"
                                    >
                                        {{ pedido.cliente.telefono }}
                                    </div>
                                </template>
                                <span v-else class="text-slate-500">—</span>
                            </td>
                            <td class="text-slate-300">
                                <ul>
                                    <li
                                        v-for="detalle in pedido.detalles"
                                        :key="detalle.id"
                                    >
                                        {{ detalle.cantidad }} ×
                                        {{
                                            detalle.repuesto?.nombre ??
                                            'Repuesto eliminado'
                                        }}
                                    </li>
                                </ul>
                                <p
                                    v-if="pedido.nota"
                                    class="mt-1 text-xs text-slate-400"
                                >
                                    Nota: {{ pedido.nota }}
                                </p>
                            </td>
                            <td
                                class="font-semibold text-slate-100 tabular-nums"
                            >
                                ${{ pedido.total.toLocaleString('es-CO') }}
                            </td>
                            <td>
                                <AppBadge
                                    :variant="badgeVariant(pedido.estado)"
                                >
                                    <span class="capitalize">
                                        {{ pedido.estado }}
                                    </span>
                                </AppBadge>
                            </td>
                            <td>
                                <div
                                    v-if="pedido.estado === 'pendiente'"
                                    class="flex justify-end gap-2"
                                >
                                    <AppButton
                                        size="sm"
                                        variant="primary"
                                        @click="atender(pedido)"
                                    >
                                        Atender
                                    </AppButton>
                                    <AppButton
                                        size="sm"
                                        variant="danger"
                                        @click="pedidoACancelar = pedido"
                                    >
                                        Cancelar
                                    </AppButton>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filas.length === 0">
                            <td
                                colspan="6"
                                class="py-12 text-center text-slate-400"
                            >
                                No hay pedidos registrados.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="border-linea border-t px-4 py-3">
                <p class="text-sm text-slate-400">
                    {{ filas.length }}
                    {{ filas.length === 1 ? 'pedido' : 'pedidos' }}
                </p>
            </div>
        </div>

        <ConfirmDialog
            :show="!!pedidoACancelar"
            title="Cancelar pedido"
            :message="`¿Cancelar el pedido de ${pedidoACancelar?.cliente?.nombre ?? 'este cliente'}?`"
            @confirm="cancelarPedido"
            @cancel="pedidoACancelar = null"
        />
    </AppLayout>
</template>
