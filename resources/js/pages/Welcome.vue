<script setup>
import { Head } from '@inertiajs/vue3';

import AppButton from '../components/AppButton.vue';

const servicios = [
    {
        titulo: 'Mantenimiento preventivo',
        descripcion:
            'Cambios de aceite, revisión de frenos, cadena y demás puntos clave para mantener tu moto en óptimas condiciones.',
    },
    {
        titulo: 'Reparación general',
        descripcion:
            'Diagnóstico y reparación de motor, transmisión, sistema eléctrico y más, con repuestos de calidad.',
    },
    {
        titulo: 'Repuestos',
        descripcion:
            'Contamos con inventario propio de repuestos originales y compatibles para las principales marcas.',
    },
    {
        titulo: 'Diagnóstico especializado',
        descripcion:
            'Revisión técnica completa para detectar fallas a tiempo y darte un presupuesto claro antes de empezar.',
    },
];

const anio = new Date().getFullYear();
</script>

<template>
    <Head title="Taller JEJ" />

    <div class="min-h-screen bg-fondo text-slate-200">
        <!-- Barra superior -->
        <header
            class="sticky top-0 z-40 border-b border-linea bg-superficie/90 backdrop-blur"
        >
            <div
                class="mx-auto flex max-w-6xl items-center justify-between px-6 py-3"
            >
                <div class="flex items-center gap-2 font-bold tracking-tight text-white">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500 text-sm font-extrabold text-slate-900"
                    >
                        J
                    </span>
                    Taller JEJ
                </div>
                <AppButton href="/login">Iniciar sesión</AppButton>
            </div>
        </header>

        <!-- Portada -->
        <section
            class="border-b border-linea bg-[radial-gradient(ellipse_at_top,rgba(245,158,11,0.14),transparent_65%)]"
        >
            <div class="mx-auto max-w-4xl px-6 py-24 text-center sm:py-32">
                <p
                    class="mb-4 inline-block rounded-full border border-amber-500/30 bg-amber-500/10 px-3 py-1 text-xs font-semibold tracking-wider text-amber-400 uppercase"
                >
                    Taller de motocicletas
                </p>
                <h1
                    class="text-4xl font-semibold tracking-tight text-slate-50 sm:text-6xl"
                >
                    Taller JEJ
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-slate-400">
                    Mantenimiento, reparación y repuestos para tu motocicleta,
                    en un solo lugar.
                </p>
                <div class="mt-10">
                    <AppButton href="/login" variant="primary">
                        Iniciar sesión
                    </AppButton>
                </div>
            </div>
        </section>

        <!-- Servicios -->
        <section class="mx-auto max-w-6xl px-6 py-20">
            <div class="mb-12 text-center">
                <h2 class="text-3xl font-semibold text-slate-50">
                    Nuestros servicios
                </h2>
                <p class="mt-2 text-sm text-slate-400">
                    Todo lo que tu moto necesita, con seguimiento claro de cada
                    trabajo.
                </p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="(servicio, indice) in servicios"
                    :key="servicio.titulo"
                    class="panel p-6 transition-colors hover:border-amber-500/40"
                >
                    <span
                        class="font-mono text-xs font-semibold tracking-widest text-amber-400"
                    >
                        0{{ indice + 1 }}
                    </span>
                    <h3 class="mt-3 text-base font-semibold text-slate-50">
                        {{ servicio.titulo }}
                    </h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-400">
                        {{ servicio.descripcion }}
                    </p>
                </div>
            </div>
        </section>

        <!-- Pie -->
        <footer class="border-t border-linea">
            <div
                class="mx-auto max-w-6xl px-6 py-6 text-center text-xs text-slate-500"
            >
                © {{ anio }} Taller JEJ. Todos los derechos reservados.
            </div>
        </footer>
    </div>
</template>
