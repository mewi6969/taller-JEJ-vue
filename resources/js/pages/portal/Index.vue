<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';

defineProps({ pedidos: Array });

function dinero(valor) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0,
    }).format(valor);
}

function fecha(valor) {
    return new Date(valor).toLocaleDateString('es-CO');
}

function totalDe(pedido) {
    return pedido.detalles.reduce(
        (suma, d) => suma + Number(d.precio_unitario) * d.cantidad,
        0,
    );
}

function cambiarEstado(pedido, estado) {
    router.patch(`/pedidos/${pedido.id}`, { estado }, { preserveScroll: true });
}

const colores = {
    pendiente: 'bg-amber-500/10 text-amber-400',
    atendido: 'bg-emerald-500/10 text-emerald-400',
    cancelado: 'bg-red-500/10 text-red-400',
};
</script>

<template>
    <AppLayout>
        <Head title="Pedidos de clientes" />
        <h1 class="mb-6 text-2xl font-bold text-white">Pedidos de clientes</h1>

        <div v-if="pedidos.length === 0" class="panel p-6 text-slate-400">
            No hay pedidos todavía.
        </div>

        <div v-else class="space-y-4">
            <div v-for="p in pedidos" :key="p.id" class="panel space-y-2 p-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="text-white">
                        #{{ p.id }} · {{ p.cliente?.nombre }}
                        {{ p.cliente?.apellido }} · Tel.
                        {{ p.cliente?.telefono }} · {{ fecha(p.created_at) }}
                    </p>
                    <span
                        class="rounded-full px-2 py-0.5 text-xs font-medium"
                        :class="colores[p.estado]"
                    >
                        {{ p.estado }}
                    </span>
                </div>
                <ul class="text-sm text-slate-300">
                    <li v-for="d in p.detalles" :key="d.id">
                        {{ d.cantidad }} × {{ d.repuesto?.nombre }} ·
                        {{ dinero(d.precio_unitario) }}
                    </li>
                </ul>
                <p v-if="p.nota" class="text-sm text-slate-400">
                    Nota: {{ p.nota }}
                </p>
                <div class="flex items-center justify-between">
                    <p class="text-white">
                        Total: <strong>{{ dinero(totalDe(p)) }}</strong>
                    </p>
                    <div v-if="p.estado === 'pendiente'" class="flex gap-2">
                        <button
                            class="rounded-md bg-emerald-600 px-3 py-1.5 text-sm text-white hover:bg-emerald-500"
                            @click="cambiarEstado(p, 'atendido')"
                        >
                            Marcar atendido
                        </button>
                        <button
                            class="border-linea hover:bg-superficie-alta rounded-md border px-3 py-1.5 text-sm text-slate-200"
                            @click="cambiarEstado(p, 'cancelado')"
                        >
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
