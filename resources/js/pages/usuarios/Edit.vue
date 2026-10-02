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
</script>

<template>
    <Head title="Editar Usuario" />

    <AppLayout>
        <div class="mx-auto max-w-2xl">
            <!-- Encabezado -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold text-slate-50">
                    Editar Usuario
                </h1>
                <p class="mt-1 text-sm text-slate-400">
                    Modifica los datos de acceso de
                    <span class="font-medium text-slate-200">
                        {{ usuario.name }}
                    </span>
                    .
                </p>
            </div>

            <!-- Tarjeta del formulario -->
            <form @submit.prevent="submit" class="panel">
                <div class="grid gap-5 p-6 sm:grid-cols-2">
                    <FormField label="Nombre" :error="form.errors.name">
                        <input type="text" v-model="form.name" class="campo" />
                    </FormField>

                    <FormField label="Email" :error="form.errors.email">
                        <input
                            type="email"
                            v-model="form.email"
                            class="campo"
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
                            class="campo"
                        />
                    </FormField>

                    <FormField
                        label="Confirmar nueva contraseña"
                        :error="form.errors.password_confirmation"
                    >
                        <input
                            type="password"
                            v-model="form.password_confirmation"
                            class="campo"
                        />
                    </FormField>

                    <div class="sm:col-span-2">
                        <FormField label="Rol" :error="form.errors.rol">
                            <select v-model="form.rol" class="campo">
                                <option value="admin">Administrador</option>
                                <option value="recepcionista">
                                    Recepcionista
                                </option>
                                <option value="mecanico">Mecánico</option>
                            </select>
                        </FormField>
                    </div>

                    <label
                        v-if="form.rol === 'mecanico'"
                        class="border-linea bg-fondo/50 flex cursor-pointer items-start gap-3 rounded-lg border p-4 sm:col-span-2"
                    >
                        <input
                            type="checkbox"
                            v-model="form.puede_crear_servicios"
                            class="mt-0.5 h-4 w-4 accent-amber-500"
                        />
                        <span>
                            <span
                                class="block text-sm font-medium text-slate-200"
                            >
                                Puede crear servicios
                            </span>
                            <span class="block text-xs text-slate-500">
                                Permiso especial para que este mecánico registre
                                nuevos servicios.
                            </span>
                        </span>
                    </label>
                </div>

                <!-- Pie con botones -->
                <div
                    class="border-linea bg-superficie-alta/40 flex justify-end gap-3 border-t px-6 py-4"
                >
                    <AppButton href="/usuarios">Cancelar</AppButton>
                    <AppButton
                        type="submit"
                        variant="primary"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Actualizando...' : 'Actualizar' }}
                    </AppButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
