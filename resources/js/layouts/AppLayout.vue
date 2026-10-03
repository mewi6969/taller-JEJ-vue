<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { onBeforeUnmount, ref, watch } from 'vue';

const page = usePage();

function logout() {
    router.post('/logout');
}

const links = [
    { href: '/dashboard', label: 'Dashboard' },
    { href: '/clientes', label: 'Clientes' },
    { href: '/motocicletas', label: 'Motocicletas' },
    { href: '/repuestos', label: 'Repuestos' },
    { href: '/servicios', label: 'Servicios' },
    { href: '/calendario', label: 'Calendario' },
    { href: '/facturas', label: 'Facturas' },
    { href: '/usuarios', label: 'Usuarios' },
];

function isActive(href) {
    return page.url.startsWith(href);
}

// Aviso de confirmación (flash)
const aviso = ref(null);
let temporizador = null;

function cerrarAviso() {
    aviso.value = null;
    clearTimeout(temporizador);
}

watch(
    () => page.props.flash,
    (flash) => {
        const texto = flash?.success ?? flash?.error;

        if (!texto) {
            return;
        }

        aviso.value = {
            tipo: flash.success ? 'success' : 'error',
            texto,
        };

        clearTimeout(temporizador);
        temporizador = setTimeout(cerrarAviso, 4000);
    },
    { immediate: true },
);

onBeforeUnmount(() => clearTimeout(temporizador));
</script>

<template>
    <div class="bg-fondo min-h-screen text-slate-200">
        <nav
            class="border-linea bg-superficie/90 sticky top-0 z-40 border-b backdrop-blur"
        >
            <div
                class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-y-2 px-6 py-3"
            >
                <div class="flex flex-wrap items-center gap-x-1 gap-y-1">
                    <Link
                        href="/dashboard"
                        class="mr-4 flex items-center gap-2 font-bold tracking-tight text-white"
                    >
                        <img
                            src="/images/logo/logo-jej-icono.png"
                            alt="Logo de Taller JEJ"
                            class="h-8 w-8 rounded-lg"
                        />
                        Taller JEJ
                    </Link>

                    <Link
                        v-for="link in links"
                        :key="link.href"
                        :href="link.href"
                        class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors"
                        :class="
                            isActive(link.href)
                                ? 'bg-amber-500/10 text-amber-400'
                                : 'hover:bg-superficie-alta text-slate-400 hover:text-slate-100'
                        "
                    >
                        {{ link.label }}
                    </Link>
                </div>

                <button
                    class="border-linea hover:bg-superficie-alta rounded-md border px-3 py-1.5 text-sm text-slate-200 transition-colors"
                    @click="logout"
                >
                    Salir
                </button>
            </div>
        </nav>

        <!-- Aviso de confirmación -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-2 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="aviso"
                role="status"
                aria-live="polite"
                class="fixed top-20 right-6 z-50 flex max-w-sm items-start gap-3 rounded-lg border px-4 py-3 text-sm shadow-lg shadow-black/30"
                :class="
                    aviso.tipo === 'success'
                        ? 'border-emerald-500/40 bg-emerald-950 text-emerald-200'
                        : 'border-red-500/40 bg-red-950 text-red-200'
                "
            >
                <span class="flex-1">{{ aviso.texto }}</span>
                <button
                    type="button"
                    class="opacity-60 transition-opacity hover:opacity-100"
                    aria-label="Cerrar aviso"
                    @click="cerrarAviso"
                >
                    ✕
                </button>
            </div>
        </Transition>

        <main class="mx-auto max-w-7xl px-6 py-8">
            <slot />
        </main>
    </div>
</template>
