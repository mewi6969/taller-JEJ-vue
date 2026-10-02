<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

import AppButton from '../../components/AppButton.vue';
import ConfirmDialog from '../../components/ConfirmDialog.vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    usuarios: Object,
    filtros: Object,
});

const buscar = ref(props.filtros.buscar || '');
const usuarioAEliminar = ref(null);

function buscarUsuarios() {
    router.get(
        '/usuarios',
        { buscar: buscar.value },
        { preserveState: true, replace: true },
    );
}

function confirmarEliminar(usuario) {
    usuarioAEliminar.value = usuario;
}

function eliminarUsuario() {
    router.delete(`/usuarios/${usuarioAEliminar.value.id}`, {
        onFinish: () => (usuarioAEliminar.value = null),
    });
}

const rolLabels = {
    admin: 'Administrador',
    recepcionista: 'Recepcionista',
    mecanico: 'Mecánico',
};

const rolClases = {
    admin: 'border-amber-500/40 bg-amber-500/10 text-amber-400',
    recepcionista: 'border-sky-500/40 bg-sky-500/10 text-sky-400',
    mecanico: 'border-emerald-500/40 bg-emerald-500/10 text-emerald-400',
};

function iniciales(usuario) {
    return (usuario.name ?? '')
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((palabra) => palabra[0])
        .join('')
        .toUpperCase();
}

function etiquetaPagina(link, index) {
    if (index === 0) return 'Anterior';
    if (index === props.usuarios.links.length - 1) return 'Siguiente';
    return link.label;
}
</script>

<template>
    <Head title="Usuarios" />

    <AppLayout>
        <!-- Encabezado -->
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-slate-50">Usuarios</h1>
                <p class="mt-1 text-sm text-slate-400">
                    Personal con acceso al sistema y su rol.
                </p>
            </div>
            <AppButton href="/usuarios/create" variant="primary">
                + Nuevo Usuario
            </AppButton>
        </div>

        <!-- Buscador -->
        <form @submit.prevent="buscarUsuarios" class="mb-6 flex gap-2">
            <div class="relative w-full max-w-sm">
                <svg
                    class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-slate-500"
                    viewBox="0 0 20 20"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                >
                    <circle cx="9" cy="9" r="6" />
                    <path d="M14 14l4 4" />
                </svg>
                <input
                    type="text"
                    v-model="buscar"
                    placeholder="Buscar por nombre"
                    class="campo pl-10"
                />
            </div>
            <AppButton type="submit">Buscar</AppButton>
        </form>

        <!-- Tarjeta con tabla -->
        <div class="panel">
            <div class="overflow-x-auto">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="usuario in usuarios.data" :key="usuario.id">
                            <td>
                                <div class="flex items-center gap-3">
                                    <span
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-500/15 text-xs font-semibold text-amber-400"
                                    >
                                        {{ iniciales(usuario) }}
                                    </span>
                                    <span class="font-medium text-slate-50">
                                        {{ usuario.name }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-slate-400">{{ usuario.email }}</td>
                            <td>
                                <span
                                    class="inline-block rounded-full border px-2.5 py-0.5 text-xs font-medium"
                                    :class="
                                        rolClases[usuario.rol] ??
                                        'border-linea text-slate-300'
                                    "
                                >
                                    {{ rolLabels[usuario.rol] ?? usuario.rol }}
                                </span>
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <AppButton
                                        :href="`/usuarios/${usuario.id}/edit`"
                                        size="sm"
                                    >
                                        Editar
                                    </AppButton>
                                    <AppButton
                                        size="sm"
                                        variant="danger"
                                        @click="confirmarEliminar(usuario)"
                                    >
                                        Eliminar
                                    </AppButton>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="usuarios.data.length === 0">
                            <td
                                colspan="4"
                                class="py-12 text-center text-slate-400"
                            >
                                No hay usuarios registrados.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pie: conteo + paginación -->
            <div
                class="border-linea flex flex-col gap-3 border-t px-4 py-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <p class="text-sm text-slate-400">
                    <template v-if="usuarios.total > 0">
                        Mostrando {{ usuarios.from }} a {{ usuarios.to }} de
                        {{ usuarios.total }} usuarios
                    </template>
                    <template v-else>Sin resultados</template>
                </p>

                <div v-if="usuarios.links.length > 3" class="flex gap-1">
                    <template
                        v-for="(link, index) in usuarios.links"
                        :key="index"
                    >
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="min-w-9 rounded-md border px-3 py-1.5 text-center text-sm transition-colors"
                            :class="
                                link.active
                                    ? 'border-amber-500 bg-amber-500 font-semibold text-slate-900'
                                    : 'border-linea hover:bg-superficie-alta text-slate-300'
                            "
                        >
                            {{ etiquetaPagina(link, index) }}
                        </Link>
                        <span
                            v-else
                            class="border-linea min-w-9 rounded-md border px-3 py-1.5 text-center text-sm text-slate-600"
                        >
                            {{ etiquetaPagina(link, index) }}
                        </span>
                    </template>
                </div>
            </div>
        </div>

        <ConfirmDialog
            :show="!!usuarioAEliminar"
            title="Eliminar usuario"
            :message="`¿Eliminar al usuario ${usuarioAEliminar?.name}? No podrá iniciar sesión, pero su historial se conserva.`"
            @confirm="eliminarUsuario"
            @cancel="usuarioAEliminar = null"
        />
    </AppLayout>
</template>
