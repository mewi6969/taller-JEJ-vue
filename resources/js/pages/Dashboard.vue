<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

import AppBadge from '../components/AppBadge.vue';
import AppLayout from '../layouts/AppLayout.vue';

const props = defineProps({
    solo_mecanico: Boolean,
    estados: Array,
    meses: Array,
    recientes: Array,
    ingresos_mes: Number,
    facturas_pendientes: Number,
    total_bajo_stock: Number,
    bajo_stock: Array,
    mecanicos: Array,
});

const mostrarBajoStock = ref(false);

function cerrarConEscape(evento) {
    if (evento.key === 'Escape') mostrarBajoStock.value = false;
}

onMounted(() => window.addEventListener('keydown', cerrarConEscape));
onUnmounted(() => window.removeEventListener('keydown', cerrarConEscape));

const estadoLabels = {
    pendiente: 'Pendiente',
    en_proceso: 'En proceso',
    terminado: 'Terminado',
    entregado: 'Entregado',
};

const estadoVariants = {
    pendiente: 'neutral',
    en_proceso: 'warning',
    terminado: 'info',
    entregado: 'ok',
};

const estadoBarras = {
    pendiente: 'bg-slate-400',
    en_proceso: 'bg-amber-500',
    terminado: 'bg-sky-500',
    entregado: 'bg-emerald-500',
};

function totalDe(estado) {
    return props.estados.find((e) => e.estado === estado)?.total ?? 0;
}

const totalServicios = computed(() =>
    props.estados.reduce((suma, e) => suma + e.total, 0),
);

const maxMes = computed(() => Math.max(1, ...props.meses.map((m) => m.total)));

const maxCarga = computed(() =>
    Math.max(1, ...props.mecanicos.map((m) => m.activos)),
);

// En la tarjeta solo se ven los 5 más críticos; el resto va en la ventana
const bajoStockResumen = computed(() => props.bajo_stock.slice(0, 5));

function moneda(valor) {
    return `$${Number(valor ?? 0).toLocaleString('es-CO')}`;
}

function porcentaje(valor, total) {
    return total > 0 ? Math.round((valor / total) * 100) : 0;
}

const tarjetas = computed(() => {
    const base = [
        {
            titulo: 'Servicios activos',
            valor: totalDe('pendiente') + totalDe('en_proceso'),
            detalle: 'Pendientes y en proceso',
            color: 'text-amber-400',
        },
        {
            titulo: 'Listos para entregar',
            valor: totalDe('terminado'),
            detalle: 'Servicios terminados',
            color: 'text-sky-400',
        },
    ];

    if (props.solo_mecanico) {
        base.push({
            titulo: 'Entregados',
            valor: totalDe('entregado'),
            detalle: 'Trabajos completados',
            color: 'text-emerald-400',
        });
        return base;
    }

    base.push(
        {
            titulo: 'Ingresos del mes',
            valor: moneda(props.ingresos_mes),
            detalle: `${props.facturas_pendientes} facturas pendientes`,
            color: 'text-emerald-400',
        },
        {
            titulo: 'Repuestos en bajo stock',
            valor: props.total_bajo_stock,
            detalle: 'Necesitan reposición',
            color:
                props.total_bajo_stock > 0 ? 'text-red-400' : 'text-slate-200',
        },
    );

    return base;
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <!-- Encabezado -->
        <div class="mb-8">
            <h1 class="text-3xl font-semibold text-slate-50">Dashboard</h1>
            <p class="mt-1 text-sm text-slate-400">
                {{
                    solo_mecanico
                        ? 'Resumen de los servicios que tienes asignados.'
                        : 'Resumen general de la actividad del taller.'
                }}
            </p>
        </div>

        <!-- Tarjetas de resumen -->
        <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div
                v-for="tarjeta in tarjetas"
                :key="tarjeta.titulo"
                class="panel p-5"
            >
                <p
                    class="text-xs font-semibold tracking-wider text-slate-400 uppercase"
                >
                    {{ tarjeta.titulo }}
                </p>
                <p
                    class="mt-2 text-3xl font-semibold tabular-nums"
                    :class="tarjeta.color"
                >
                    {{ tarjeta.valor }}
                </p>
                <p class="mt-1 text-xs text-slate-500">{{ tarjeta.detalle }}</p>
            </div>
        </div>

        <div class="mb-6 grid gap-6 lg:grid-cols-3">
            <!-- Servicios por mes -->
            <div class="panel p-6 lg:col-span-2">
                <h2 class="text-sm font-semibold text-slate-100">
                    Servicios por mes
                </h2>
                <p class="mb-6 text-xs text-slate-500">Últimos 6 meses</p>

                <div class="flex h-44 items-end gap-3">
                    <div
                        v-for="mes in meses"
                        :key="mes.etiqueta"
                        class="flex h-full flex-1 flex-col items-center justify-end gap-2"
                    >
                        <span
                            class="text-xs font-semibold text-slate-300 tabular-nums"
                        >
                            {{ mes.total }}
                        </span>
                        <div
                            class="w-full max-w-12 rounded-t-md bg-amber-500/80"
                            :style="{
                                height: `${Math.max((mes.total / maxMes) * 100, 3)}%`,
                            }"
                        />
                        <span class="text-xs text-slate-400">{{
                            mes.etiqueta
                        }}</span>
                    </div>
                </div>
            </div>

            <!-- Servicios por estado -->
            <div class="panel p-6">
                <h2 class="text-sm font-semibold text-slate-100">
                    Servicios por estado
                </h2>
                <p class="mb-6 text-xs text-slate-500">
                    {{ totalServicios }} en total
                </p>

                <div class="space-y-4">
                    <div v-for="item in estados" :key="item.estado">
                        <div class="mb-1.5 flex justify-between text-sm">
                            <span class="text-slate-300">
                                {{ estadoLabels[item.estado] }}
                            </span>
                            <span
                                class="font-semibold text-slate-100 tabular-nums"
                            >
                                {{ item.total }}
                            </span>
                        </div>
                        <div
                            class="bg-superficie-alta h-2 overflow-hidden rounded-full"
                        >
                            <div
                                class="h-full rounded-full"
                                :class="estadoBarras[item.estado]"
                                :style="{
                                    width: `${porcentaje(item.total, totalServicios)}%`,
                                }"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Últimos servicios -->
            <div
                class="panel"
                :class="solo_mecanico ? 'lg:col-span-3' : 'lg:col-span-2'"
            >
                <div class="border-linea border-b px-6 py-4">
                    <h2 class="text-sm font-semibold text-slate-100">
                        Últimos servicios
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="tabla">
                        <thead>
                            <tr>
                                <th>Moto</th>
                                <th>Cliente</th>
                                <th>Estado</th>
                                <th>Costo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="servicio in recientes"
                                :key="servicio.id"
                            >
                                <td>
                                    <span
                                        class="rounded-md border border-amber-500/40 bg-amber-500/10 px-2.5 py-1 font-mono text-xs font-semibold tracking-widest text-amber-400 uppercase"
                                    >
                                        {{ servicio.placa }}
                                    </span>
                                </td>
                                <td class="font-medium text-slate-50">
                                    {{ servicio.cliente }}
                                </td>
                                <td>
                                    <AppBadge
                                        :variant="
                                            estadoVariants[servicio.estado]
                                        "
                                    >
                                        {{ estadoLabels[servicio.estado] }}
                                    </AppBadge>
                                </td>
                                <td class="text-slate-100 tabular-nums">
                                    {{ moneda(servicio.costo_total) }}
                                </td>
                            </tr>
                            <tr v-if="recientes.length === 0">
                                <td
                                    colspan="4"
                                    class="py-10 text-center text-slate-400"
                                >
                                    Aún no hay servicios registrados.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Columna lateral (no se muestra a mecánicos) -->
            <div v-if="!solo_mecanico" class="space-y-6">
                <div class="panel p-6">
                    <div class="mb-4 flex items-center justify-between gap-2">
                        <h2 class="text-sm font-semibold text-slate-100">
                            Repuestos en bajo stock
                        </h2>
                        <button
                            v-if="total_bajo_stock > 0"
                            type="button"
                            class="border-linea hover:bg-superficie-alta rounded-md border px-2.5 py-1 text-xs font-medium text-amber-400 transition-colors"
                            @click="mostrarBajoStock = true"
                        >
                            Ver todos ({{ total_bajo_stock }})
                        </button>
                    </div>
                    <ul v-if="bajoStockResumen.length" class="space-y-3">
                        <li
                            v-for="repuesto in bajoStockResumen"
                            :key="repuesto.id"
                            class="flex items-center justify-between text-sm"
                        >
                            <span class="text-slate-300">{{
                                repuesto.nombre
                            }}</span>
                            <span
                                class="font-semibold text-red-400 tabular-nums"
                            >
                                {{ repuesto.cantidad }}
                                <span class="font-normal text-slate-500">
                                    / mín. {{ repuesto.cantidad_minima }}
                                </span>
                            </span>
                        </li>
                    </ul>
                    <p v-else class="text-sm text-slate-500">
                        Todo el inventario está en orden.
                    </p>
                </div>

                <div class="panel p-6">
                    <h2 class="mb-1 text-sm font-semibold text-slate-100">
                        Disponibilidad de mecánicos
                    </h2>
                    <p class="mb-4 text-xs text-slate-500">
                        Servicios activos por mecánico
                    </p>
                    <ul v-if="mecanicos.length" class="space-y-4">
                        <li v-for="mecanico in mecanicos" :key="mecanico.id">
                            <div class="mb-1.5 flex justify-between text-sm">
                                <span class="text-slate-300">{{
                                    mecanico.name
                                }}</span>
                                <span
                                    class="font-semibold text-slate-100 tabular-nums"
                                >
                                    {{ mecanico.activos }}
                                </span>
                            </div>
                            <div
                                class="bg-superficie-alta h-2 overflow-hidden rounded-full"
                            >
                                <div
                                    class="h-full rounded-full bg-amber-500"
                                    :style="{
                                        width: `${porcentaje(mecanico.activos, maxCarga)}%`,
                                    }"
                                />
                            </div>
                        </li>
                    </ul>
                    <p v-else class="text-sm text-slate-500">
                        No hay mecánicos registrados.
                    </p>
                </div>
            </div>
        </div>

        <!-- Ventana: todos los repuestos en bajo stock -->
        <div
            v-if="mostrarBajoStock"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
            @click.self="mostrarBajoStock = false"
        >
            <div class="panel flex max-h-[80vh] w-full max-w-lg flex-col">
                <div
                    class="border-linea flex items-start justify-between gap-4 border-b px-6 py-4"
                >
                    <div>
                        <h2 class="text-base font-semibold text-slate-50">
                            Repuestos en bajo stock
                        </h2>
                        <p class="text-xs text-slate-400">
                            {{ bajo_stock.length }} repuestos por debajo o en su
                            cantidad mínima
                        </p>
                    </div>
                    <button
                        type="button"
                        class="hover:bg-superficie-alta rounded-md px-2 py-1 text-slate-400 transition-colors hover:text-slate-100"
                        aria-label="Cerrar"
                        @click="mostrarBajoStock = false"
                    >
                        ✕
                    </button>
                </div>

                <ul class="divide-linea divide-y overflow-y-auto">
                    <li
                        v-for="repuesto in bajo_stock"
                        :key="repuesto.id"
                        class="flex items-center justify-between gap-4 px-6 py-3 text-sm"
                    >
                        <div>
                            <p class="font-medium text-slate-50">
                                {{ repuesto.nombre }}
                            </p>
                            <p class="text-xs text-slate-400">
                                Quedan
                                <span
                                    class="font-semibold text-red-400 tabular-nums"
                                >
                                    {{ repuesto.cantidad }}
                                </span>
                                · mínimo {{ repuesto.cantidad_minima }}
                            </p>
                        </div>
                        <Link
                            :href="`/repuestos/${repuesto.id}/edit`"
                            class="border-linea hover:bg-superficie-alta shrink-0 rounded-md border px-3 py-1.5 text-xs font-medium text-slate-100 transition-colors"
                        >
                            Reponer
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
