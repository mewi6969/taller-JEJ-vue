<script setup>
import { Head, useForm } from '@inertiajs/vue3';

import AppButton from '../../components/AppButton.vue';
import FormField from '../../components/FormField.vue';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    usuario: Object,
});

const form = useForm({
    name: props.usuario.name,
    email: props.usuario.email,
    password: '',
    password_confirmation: '',
    rol: props.usuario.rol,
    puede_crear_servicios: props.usuario.puede_crear_servicios,
});

function submit() {
    form.put(`/usuarios/${props.usuario.id}`);
}

const inputClasses =
    'rounded-md border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100';
</script>

<template>
    <Head title="Editar Usuario" />

    <AppLayout>
        <h1 class="mb-6 text-2xl font-semibold text-slate-100">
            Editar Usuario
        </h1>

        <form @submit.prevent="submit" class="flex max-w-md flex-col gap-4">
            <FormField label="Nombre" :error="form.errors.name">
                <input
                    type="text"
                    v-model="form.name"
                    :class="inputClasses"
                />
            </FormField>

            <FormField label="Email" :error="form.errors.email">
                <input
                    type="email"
                    v-model="form.email"
                    :class="inputClasses"
                />
            </FormField>

            <FormField
                label="Nueva contraseña (opcional)"
                :error="form.errors.password"
            >
                <input
                    type="password"
                    v-model="form.password"
                    placeholder="Dejar en blanco para no cambiarla"
                    :class="inputClasses"
                />
            </FormField>

            <FormField
                label="Confirmar nueva contraseña"
                :error="form.errors.password_confirmation"
            >
                <input
                    type="password"
                    v-model="form.password_confirmation"
                    :class="inputClasses"
                />
            </FormField>

            <FormField label="Rol" :error="form.errors.rol">
                <select v-model="form.rol" :class="inputClasses">
                    <option value="admin">Administrador</option>
                    <option value="recepcionista">Recepcionista</option>
                    <option value="mecanico">Mecánico</option>
                </select>
            </FormField>

            <label
                v-if="form.rol === 'mecanico'"
                class="flex items-center gap-2 text-sm text-slate-300"
            >
                <input type="checkbox" v-model="form.puede_crear_servicios" />
                Puede crear servicios (mecánico con permiso especial)
            </label>

            <div class="mt-2 flex gap-3">
                <AppButton
                    type="submit"
                    variant="primary"
                    :disabled="form.processing"
                >
                    Actualizar
                </AppButton>
                <AppButton href="/usuarios">Cancelar</AppButton>
            </div>
        </form>
    </AppLayout>
</template>
