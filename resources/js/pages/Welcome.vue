<script setup>
import { Head } from '@inertiajs/vue3';
import { onBeforeUnmount, onMounted, ref } from 'vue';

import AppButton from '../components/AppButton.vue';

// Datos del negocio: reemplázalos por los reales
const contacto = {
    telefono: '300 000 0000',
    whatsapp: '573000000000',
    correo: 'contacto@tallerjej.com',
    direccion: 'Calle 00 # 00-00',
    horario: 'Lunes a sábado, 8:00 a. m. a 6:00 p. m.',
};

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

// Carrusel. Para usar una foto, pon su ruta en "imagen",
// por ejemplo: '/images/taller/foto-1.jpg'. Sin foto se muestra un fondo de color.
const diapositivas = [
    {
        titulo: 'Mantenimiento a tiempo',
        texto: 'Cuidamos tu moto para que no te deje tirado.',
        imagen: null,
    },
    {
        titulo: 'Reparaciones con garantía',
        texto: 'Diagnóstico claro y presupuesto antes de empezar.',
        imagen: null,
    },
    {
        titulo: 'Repuestos de calidad',
        texto: 'Inventario propio de las principales marcas.',
        imagen: null,
    },
];

const actual = ref(0);
const pausado = ref(false);
let temporizador = null;
let inicioToque = 0;

function ir(indice) {
    const total = diapositivas.length;
    actual.value = (indice + total) % total;
}

function siguiente() {
    ir(actual.value + 1);
}

function anterior() {
    ir(actual.value - 1);
}

function alIniciarToque(evento) {
    inicioToque = evento.changedTouches[0].clientX;
}

function alTerminarToque(evento) {
    const diferencia = evento.changedTouches[0].clientX - inicioToque;

    if (Math.abs(diferencia) < 40) {
        return;
    }

    if (diferencia < 0) {
        siguiente();
    } else {
        anterior();
    }
}

onMounted(() => {
    temporizador = setInterval(() => {
        if (!pausado.value) {
            siguiente();
        }
    }, 5000);
});

onBeforeUnmount(() => clearInterval(temporizador));

const anio = new Date().getFullYear();
</script>

<template>
    <Head title="Taller JEJ" />

    <div class="bg-fondo min-h-screen text-slate-200">
        <!-- Barra superior -->
        <header
            class="border-linea bg-superficie/90 sticky top-0 z-40 border-b backdrop-blur"
        >
            <div
                class="mx-auto flex max-w-6xl items-center justify-between px-6 py-3"
            >
                <div
                    class="flex items-center gap-2 font-bold tracking-tight text-white"
                >
                    <img
                        src="/images/logo/logo-jej-icono.png"
                        alt="Logo de Taller JEJ"
                        class="h-8 w-8 rounded-lg"
                    />
                    Taller JEJ
                </div>
                <AppButton href="/login">Iniciar sesión</AppButton>
            </div>
        </header>

        <!-- Portada -->
        <section
            class="border-linea border-b bg-[radial-gradient(ellipse_at_top,rgba(245,158,11,0.14),transparent_65%)]"
        >
            <div class="mx-auto max-w-4xl px-6 py-16 text-center sm:py-24">
                <img
                    src="/images/logo/logo-jej.png"
                    alt="Logo de Taller JEJ"
                    class="mx-auto mb-8 h-40 w-40 rounded-3xl sm:h-48 sm:w-48"
                />
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

        <!-- Carrusel -->
        <section class="mx-auto max-w-6xl px-6 pt-16">
            <div
                class="border-linea relative overflow-hidden rounded-2xl border"
                @mouseenter="pausado = true"
                @mouseleave="pausado = false"
                @touchstart.passive="alIniciarToque"
                @touchend.passive="alTerminarToque"
            >
                <div
                    class="flex transition-transform duration-500 ease-out"
                    :style="{ transform: `translateX(-${actual * 100}%)` }"
                >
                    <div
                        v-for="(diapositiva, indice) in diapositivas"
                        :key="diapositiva.titulo"
                        class="relative flex h-64 w-full shrink-0 items-end bg-gradient-to-br from-slate-800 via-slate-900 to-amber-950 sm:h-96"
                        :aria-hidden="indice !== actual"
                    >
                        <img
                            v-if="diapositiva.imagen"
                            :src="diapositiva.imagen"
                            :alt="diapositiva.titulo"
                            class="absolute inset-0 h-full w-full object-cover"
                        />
                        <div
                            class="relative w-full bg-gradient-to-t from-black/70 to-transparent p-6 sm:p-10"
                        >
                            <h2
                                class="text-2xl font-semibold text-white sm:text-3xl"
                            >
                                {{ diapositiva.titulo }}
                            </h2>
                            <p class="mt-1 text-sm text-slate-200 sm:text-base">
                                {{ diapositiva.texto }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Flechas -->
                <button
                    type="button"
                    class="absolute top-1/2 left-3 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-black/50 text-xl text-white transition-colors hover:bg-black/70"
                    aria-label="Imagen anterior"
                    @click="anterior"
                >
                    ‹
                </button>
                <button
                    type="button"
                    class="absolute top-1/2 right-3 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-black/50 text-xl text-white transition-colors hover:bg-black/70"
                    aria-label="Imagen siguiente"
                    @click="siguiente"
                >
                    ›
                </button>

                <!-- Puntos -->
                <div
                    class="absolute bottom-3 left-1/2 flex -translate-x-1/2 gap-2"
                >
                    <button
                        v-for="(diapositiva, indice) in diapositivas"
                        :key="diapositiva.titulo"
                        type="button"
                        class="h-2.5 rounded-full transition-all"
                        :class="
                            indice === actual
                                ? 'w-6 bg-amber-400'
                                : 'w-2.5 bg-white/50 hover:bg-white/80'
                        "
                        :aria-label="`Ir a la imagen ${indice + 1}`"
                        @click="ir(indice)"
                    />
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

        <!-- Contacto -->
        <section class="border-linea border-t">
            <div class="mx-auto max-w-6xl px-6 py-20">
                <div class="mb-12 text-center">
                    <h2 class="text-3xl font-semibold text-slate-50">
                        Contáctanos
                    </h2>
                    <p class="mt-2 text-sm text-slate-400">
                        Escríbenos o visítanos, con gusto te atendemos.
                    </p>
                </div>

                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="panel p-6">
                        <p
                            class="text-xs font-semibold tracking-widest text-amber-400 uppercase"
                        >
                            Teléfono
                        </p>
                        <p class="mt-2 text-sm text-slate-200">
                            {{ contacto.telefono }}
                        </p>
                    </div>
                    <div class="panel p-6">
                        <p
                            class="text-xs font-semibold tracking-widest text-amber-400 uppercase"
                        >
                            Correo
                        </p>
                        <p class="mt-2 text-sm break-words text-slate-200">
                            {{ contacto.correo }}
                        </p>
                    </div>
                    <div class="panel p-6">
                        <p
                            class="text-xs font-semibold tracking-widest text-amber-400 uppercase"
                        >
                            Dirección
                        </p>
                        <p class="mt-2 text-sm text-slate-200">
                            {{ contacto.direccion }}
                        </p>
                    </div>
                    <div class="panel p-6">
                        <p
                            class="text-xs font-semibold tracking-widest text-amber-400 uppercase"
                        >
                            Horario
                        </p>
                        <p class="mt-2 text-sm text-slate-200">
                            {{ contacto.horario }}
                        </p>
                    </div>
                </div>

                <div class="mt-10 text-center">
                    <AppButton
                        :href="`https://wa.me/${contacto.whatsapp}`"
                        variant="primary"
                    >
                        Escríbenos por WhatsApp
                    </AppButton>
                </div>
            </div>
        </section>

        <!-- Pie -->
        <footer class="border-linea border-t">
            <div
                class="mx-auto max-w-6xl px-6 py-6 text-center text-xs text-slate-500"
            >
                © {{ anio }} Taller JEJ. Todos los derechos reservados.
            </div>
        </footer>
    </div>
</template>
