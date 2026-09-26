<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    usuarios: Object,
    filtros: Object,
});

const buscar = ref(props.filtros.buscar || '');

function buscarUsuarios() {
    router.get('/usuarios', { buscar: buscar.value }, {
        preserveState: true,
        replace: true,
    });
}

function eliminarUsuario(usuario) {
    if (confirm(`¿Eliminar al usuario ${usuario.name}? No podrá iniciar sesión, pero su historial se conserva.`)) {
        router.delete(`/usuarios/${usuario.id}`);
    }
}

const rolLabels = {
    admin: 'Administrador',
    recepcionista: 'Recepcionista',
    mecanico: 'Mecánico',
};
</script>

<template>
    <Head title="Usuarios" />

    <AppLayout>
        <div class="header-row">
            <h1>Usuarios</h1>
            <Link href="/usuarios/create" class="btn btn-primary">+ Nuevo Usuario</Link>
        </div>

        <form @submit.prevent="buscarUsuarios" class="search-row">
            <input type="text" v-model="buscar" placeholder="Buscar por nombre" />
            <button type="submit" class="btn">Buscar</button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="usuario in usuarios.data" :key="usuario.id">
                    <td>{{ usuario.name }}</td>
                    <td>{{ usuario.email }}</td>
                    <td>{{ rolLabels[usuario.rol] }}</td>
                    <td class="actions">
                        <Link :href="`/usuarios/${usuario.id}/edit`" class="btn btn-sm">Editar</Link>
                        <button class="btn btn-sm btn-danger" @click="eliminarUsuario(usuario)">Eliminar</button>
                    </td>
                </tr>
                <tr v-if="usuarios.data.length === 0">
                    <td colspan="4" class="empty">No hay usuarios registrados.</td>
                </tr>
            </tbody>
        </table>

        <div class="pagination">
            <template v-for="link in usuarios.links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="page-link"
                    :class="{ active: link.active }"
                    v-html="link.label"
                />
                <span v-else class="page-link disabled" v-html="link.label" />
            </template>
        </div>
    </AppLayout>
</template>

<style scoped>
.header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
.search-row { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; }
.search-row input { flex: 1; max-width: 320px; padding: 0.5rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #e2e8f0; }
.table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
.table th, .table td { padding: 0.6rem; border-bottom: 1px solid #334155; text-align: left; }
.actions { display: flex; gap: 0.5rem; }
.empty { text-align: center; color: #94a3b8; }
.btn { display: inline-block; padding: 0.5rem 0.9rem; border-radius: 6px; border: 1px solid #475569; background: transparent; color: #e2e8f0; cursor: pointer; text-decoration: none; font-size: 0.9rem; }
.btn-primary { background: #3b82f6; border-color: #3b82f6; color: white; }
.btn-sm { padding: 0.3rem 0.6rem; font-size: 0.8rem; }
.btn-danger { border-color: #b91c1c; color: #fca5a5; }
.pagination { display: flex; gap: 0.4rem; flex-wrap: wrap; }
.page-link { padding: 0.35rem 0.7rem; border-radius: 6px; border: 1px solid #334155; color: #e2e8f0; text-decoration: none; font-size: 0.85rem; }
.page-link.active { background: #3b82f6; border-color: #3b82f6; }
.page-link.disabled { opacity: 0.4; }
</style>
