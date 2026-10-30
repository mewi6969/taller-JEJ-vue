<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

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

const colores = {
    pendiente: 'bg-amber-500/10 text-amber-400',
    atendido: 'bg-emerald-500/10 text-emerald-400',
    cancelado: 'bg-red-500/10 text-red-400',
};
</script>

<template>
    <AppLayout>
        <Head title="Mis pedidos" />
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">Mis pedidos</h1>
            <Link
                href="/portal/catalogo"
                class="rounded-md bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-amber-400"
            >
                Nuevo pedido
            </Link>
        </div>

        <div v-if="pedidos.length === 0" class="panel p-6 text-slate-400">
            Aún no has hecho pedidos.
        </div>

        <div v-else class="space-y-4">
            <div v-for="p in pedidos" :key="p.id" class="panel space-y-2 p-4">
                <div class="flex items-center justify-between">
                    <p class="text-white">
                        Pedido #{{ p.id }} · {{ fecha(p.created_at) }}
                    </p>
                    <span
                        class="rounded-full px-2 py-0.5 text-xs font-medium"
                        :class="colores[p.estado]"
                    >
                        {{ p.estado }}
                    </span>
                </div>
                <p v-if="p.motocicleta" class="text-sm text-slate-300">
                    Moto:
                    <strong class="text-white">{{
                        p.motocicleta.placa
                    }}</strong>
                    — {{ p.motocicleta.marca }} {{ p.motocicleta.modelo }}
                </p>
                <ul class="text-sm text-slate-300">
                    <li v-for="d in p.detalles" :key="d.id">
                        {{ d.cantidad }} × {{ d.repuesto?.nombre }} ·
                        {{ dinero(d.precio_unitario) }}
                    </li>
                </ul>
                <p v-if="p.nota" class="text-sm text-slate-400">
                    Nota: {{ p.nota }}
                </p>
                <p class="text-right text-white">
                    Total: <strong>{{ dinero(totalDe(p)) }}</strong>
                </p>
            </div>
        </div>
    </AppLayout>
</template>
