<script setup>
import { Link, router } from '@inertiajs/vue3';

function logout() {
    router.post('/logout');
}
</script>

<template>
    <div class="app-shell">
        <nav class="app-nav">
            <div class="app-nav-left">
                <span class="app-brand">Taller de Motos</span>
                <Link href="/clientes" class="app-link">Clientes</Link>
                <Link href="/motocicletas" class="app-link">Motocicletas</Link>
                <Link href="/repuestos" class="app-link">Repuestos</Link>
                <Link href="/servicios" class="app-link">Servicios</Link>
                <Link href="/usuarios" class="app-link">Usuarios</Link>
            </div>
            <button class="app-logout" @click="logout">Salir</button>
        </nav>

        <main class="app-content">
            <slot />
        </main>
    </div>
</template>

<style scoped>
.app-shell {
    min-height: 100vh;
    background: #0f172a;
    color: #e2e8f0;
}
.app-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    background: #1e293b;
    border-bottom: 1px solid #334155;
}
.app-nav-left {
    display: flex;
    align-items: center;
    gap: 1.25rem;
}
.app-brand {
    font-weight: 700;
}
.app-link {
    color: #93c5fd;
    text-decoration: none;
}
.app-link:hover {
    text-decoration: underline;
}
.app-logout {
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    border: 1px solid #475569;
    background: transparent;
    color: #e2e8f0;
    cursor: pointer;
}
.app-content {
    padding: 2rem;
}
</style>
