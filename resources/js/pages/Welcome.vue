<script setup>
import { Head } from '@inertiajs/vue3';
import L from 'leaflet';
import iconoMarcador from 'leaflet/dist/images/marker-icon.png';
import iconoMarcador2x from 'leaflet/dist/images/marker-icon-2x.png';
import sombraMarcador from 'leaflet/dist/images/marker-shadow.png';
import 'leaflet/dist/leaflet.css';
import { onBeforeUnmount, onMounted, ref } from 'vue';

import AppButton from '../components/AppButton.vue';

// Datos del negocio: número de prueba. Cámbialo por el real antes de publicar.
const contacto = {
    telefono: '320 618 1331',
    whatsapp: '573206181331',
    correo: 'contacto@tallerjej.com',
    direccion: 'Calle 00 # 00-00',
    horario: 'Lunes a sábado, 8:00 a. m. a 6:00 p. m.',
};

// Ubicación del taller (latitud y longitud)
const ubicacion = {
    lat: 3.2492102418745117,
    lng: -76.23169610233815,
};

const enlaceComoLlegar = `https://www.google.com/maps/dir/?api=1&destination=${ubicacion.lat},${ubicacion.lng}`;

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

// Carrusel. Las fotos van en public/images/taller/ con estos nombres.
// Si falta una foto, se muestra un fondo de color en su lugar.
const diapositivas = [
    {
        titulo: 'Mantenimiento a tiempo',
        texto: 'Cuidamos tu moto para que no te deje tirado.',
        imagen: '/images/taller/foto-1.jpg',
    },
    {
        titulo: 'Reparaciones con garantía',
        texto: 'Diagnóstico claro y presupuesto antes de empezar.',
        imagen: '/images/taller/foto-2.jpg',
    },
    {
        titulo: 'Repuestos de calidad',
        texto: 'Inventario propio de las principales marcas.',
        imagen: '/images/taller/foto-3.jpg',
    },
    {
        titulo: 'Diagnóstico especializado',
        texto: 'Detectamos fallas a tiempo para evitarte sorpresas.',
        imagen: '/images/taller/foto-4.jpg',
    },
    {
        titulo: 'Atención cercana',
        texto: 'Te explicamos cada trabajo y te mantenemos informado.',
        imagen: '/images/taller/foto-5.jpg',
    },
];

const actual = ref(0);
const pausado = ref(false);
const contenedorMapa = ref(null);
let temporizador = null;
let mapa = null;
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

function crearMapa() {
    const icono = L.icon({
        iconUrl: iconoMarcador,
        iconRetinaUrl: iconoMarcador2x,
        shadowUrl: sombraMarcador,
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41],
    });

    mapa = L.map(contenedorMapa.value, { scrollWheelZoom: false }).setView(
        [ubicacion.lat, ubicacion.lng],
        17,
    );

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution:
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(mapa);

    L.marker([ubicacion.lat, ubicacion.lng], { icon: icono })
        .addTo(mapa)
        .bindPopup('Taller JEJ');
}

onMounted(() => {
    temporizador = setInterval(() => {
        if (!pausado.value) {
            siguiente();
        }
    }, 5000);

    crearMapa();
});

onBeforeUnmount(() => {
    clearInterval(temporizador);

    if (mapa) {
        mapa.remove();
    }
});

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
        <section
            class="border-linea relative overflow-hidden border-b py-10 sm:py-16"
        >
            <!-- Fondo: las mismas fotos, difuminadas, que cambian con el carrusel -->
            <div class="absolute inset-0" aria-hidden="true">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-amber-950"
                />
                <template
                    v-for="(diapositiva, indice) in diapositivas"
                    :key="diapositiva.titulo"
                >
                    <img
                        v-if="diapositiva.imagen"
                        :src="diapositiva.imagen"
                        alt=""
                        class="absolute inset-0 h-full w-full scale-110 object-cover blur-2xl transition-opacity duration-1000"
                        :class="indice === actual ? 'opacity-70' : 'opacity-0'"
                        @error="$event.target.style.display = 'none'"
                    />
                </template>
                <div class="absolute inset-0 bg-black/40" />
            </div>

            <div class="relative mx-auto max-w-6xl px-6">
                <div
                    class="border-linea relative overflow-hidden rounded-2xl border shadow-2xl shadow-black/50"
                    @mouseenter="pausado = true"
                    @mouseleave="pausado = false"
                    @touchstart.passive="alIniciarToque"
                    @touchend.passive="alTerminarToque"
                >
                    <div
                        class="flex transition-transform duration-700 ease-out"
                        :style="{ transform: `translateX(-${actual * 100}%)` }"
                    >
                        <div
                            v-for="(diapositiva, indice) in diapositivas"
                            :key="diapositiva.titulo"
                            class="relative flex h-72 w-full shrink-0 items-end overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-amber-950 sm:h-[32rem]"
                            :aria-hidden="indice !== actual"
                        >
                            <template v-if="diapositiva.imagen">
                                <!-- Copia difuminada que rellena los lados -->
                                <img
                                    :src="diapositiva.imagen"
                                    alt=""
                                    aria-hidden="true"
                                    class="absolute inset-0 h-full w-full scale-125 object-cover opacity-60 blur-xl"
                                    @error="
                                        $event.target.style.display = 'none'
                                    "
                                />
                                <!-- Foto completa, sin recortar -->
                                <img
                                    :src="diapositiva.imagen"
                                    :alt="diapositiva.titulo"
                                    class="absolute inset-0 h-full w-full object-contain"
                                    @error="
                                        $event.target.style.display = 'none'
                                    "
                                />
                            </template>
                            <div
                                class="relative w-full bg-gradient-to-t from-black/80 to-transparent p-6 pb-12 sm:p-10 sm:pb-14"
                            >
                                <h2
                                    class="text-2xl font-semibold text-white sm:text-4xl"
                                >
                                    {{ diapositiva.titulo }}
                                </h2>
                                <p
                                    class="mt-1 text-sm text-slate-200 sm:text-lg"
                                >
                                    {{ diapositiva.texto }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Flechas -->
                    <button
                        type="button"
                        class="absolute top-1/2 left-3 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-black/10 text-xl text-white/70 backdrop-blur-sm transition-colors hover:bg-black/30 hover:text-white"
                        aria-label="Imagen anterior"
                        @click="anterior"
                    >
                        ‹
                    </button>
                    <button
                        type="button"
                        class="absolute top-1/2 right-3 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-black/10 text-xl text-white/70 backdrop-blur-sm transition-colors hover:bg-black/30 hover:text-white"
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
                                    ? 'w-6 bg-amber-400/80'
                                    : 'w-2.5 bg-white/30 hover:bg-white/50'
                            "
                            :aria-label="`Ir a la imagen ${indice + 1}`"
                            @click="ir(indice)"
                        />
                    </div>
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

        <!-- Contacto y ubicación -->
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

                <!-- Mapa -->
                <div
                    class="border-linea relative isolate z-0 mt-10 overflow-hidden rounded-2xl border"
                >
                    <div
                        ref="contenedorMapa"
                        class="h-72 w-full sm:h-96"
                        role="img"
                        aria-label="Mapa con la ubicación del Taller JEJ"
                    />
                </div>

                <div
                    class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row"
                >
                    <a
                        :href="`https://wa.me/${contacto.whatsapp}`"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center rounded-lg bg-amber-500 px-5 py-2.5 text-sm font-semibold text-slate-950 transition-colors hover:bg-amber-400"
                    >
                        Escríbenos por WhatsApp
                    </a>
                    <a
                        :href="enlaceComoLlegar"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="border-linea inline-flex items-center justify-center rounded-lg border px-5 py-2.5 text-sm font-semibold text-slate-200 transition-colors hover:border-amber-500/40 hover:text-white"
                    >
                        Cómo llegar
                    </a>
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
