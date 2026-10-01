<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';

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
    { href: '/facturas', label: 'Facturas' },
    { href: '/usuarios', label: 'Usuarios' },
];

function isActive(href) {
    return page.url.startsWith(href);
}
</script>

<template>
    <div class="min-h-screen bg-fondo text-slate-200">
        <nav
            class="sticky top-0 z-40 border-b border-linea bg-superficie/90 backdrop-blur"
        >
            <div
                class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-y-2 px-6 py-3"
            >
                <div class="flex flex-wrap items-center gap-x-1 gap-y-1">
                    <Link
                        href="/dashboard"
                        class="mr-4 flex items-center gap-2 font-bold tracking-tight text-white"
                    >
                        <span
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500 text-sm font-extrabold text-slate-900"
                        >
                            J
                        </span>
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
                                : 'text-slate-400 hover:bg-superficie-alta hover:text-slate-100'
                        "
                    >
                        {{ link.label }}
                    </Link>
                </div>

                <button
                    class="rounded-md border border-linea px-3 py-1.5 text-sm text-slate-200 transition-colors hover:bg-superficie-alta"
                    @click="logout"
                >
                    Salir
                </button>
            </div>
        </nav>

        <main class="mx-auto max-w-7xl px-6 py-8">
            <slot />
        </main>
    </div>
</template>
