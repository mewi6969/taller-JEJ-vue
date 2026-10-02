<script setup>
import esLocale from '@fullcalendar/core/locales/es';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import FullCalendar from '@fullcalendar/vue3';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

import AppLayout from '../layouts/AppLayout.vue';

const props = defineProps({
    servicios: Array,
});

const estados = {
    pendiente: { etiqueta: 'Pendiente', fondo: '#64748b', texto: '#f8fafc' },
    en_proceso: { etiqueta: 'En proceso', fondo: '#f59e0b', texto: '#1c1917' },
    terminado: { etiqueta: 'Terminado', fondo: '#0ea5e9', texto: '#082f49' },
    entregado: { etiqueta: 'Entregado', fondo: '#10b981', texto: '#022c22' },
};

const eventos = computed(() =>
    props.servicios.map((servicio) => {
        const estado = estados[servicio.estado] ?? estados.pendiente;

        return {
            id: String(servicio.id),
            title: `${servicio.placa} · ${servicio.cliente}`,
            start: servicio.inicio,
            end: servicio.fin,
            allDay: true,
            backgroundColor: estado.fondo,
            borderColor: estado.fondo,
            textColor: estado.texto,
            extendedProps: {
                estado: estado.etiqueta,
                mecanico: servicio.mecanico,
            },
        };
    }),
);

const opciones = computed(() => ({
    plugins: [dayGridPlugin, interactionPlugin],
    locale: esLocale,
    initialView: 'dayGridMonth',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,dayGridWeek',
    },
    height: 'auto',
    dayMaxEvents: 3,
    events: eventos.value,
    eventClick: (info) => {
        info.jsEvent.preventDefault();
        router.visit(`/servicios/${info.event.id}/edit`);
    },
    eventDidMount: (info) => {
        const { estado, mecanico } = info.event.extendedProps;
        info.el.title = `${info.event.title}\n${estado}${
            mecanico ? ` · ${mecanico}` : ' · Sin mecánico'
        }`;
    },
}));
</script>

<template>
    <Head title="Calendario" />

    <AppLayout>
        <!-- Encabezado -->
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-slate-50">Calendario</h1>
                <p class="mt-1 text-sm text-slate-400">
                    Ocupación del taller: cada barra va desde el ingreso hasta
                    la entrega del servicio.
                </p>
            </div>

            <!-- Leyenda de colores -->
            <ul class="flex flex-wrap gap-4 text-xs text-slate-400">
                <li
                    v-for="estado in estados"
                    :key="estado.etiqueta"
                    class="flex items-center gap-1.5"
                >
                    <span
                        class="h-2.5 w-2.5 rounded-full"
                        :style="{ backgroundColor: estado.fondo }"
                    />
                    {{ estado.etiqueta }}
                </li>
            </ul>
        </div>

        <!-- Calendario -->
        <div class="panel p-4 sm:p-6">
            <FullCalendar :options="opciones" />
        </div>

        <p v-if="servicios.length === 0" class="mt-4 text-center text-sm text-slate-500">
            Aún no hay servicios para mostrar en el calendario.
        </p>
    </AppLayout>
</template>
