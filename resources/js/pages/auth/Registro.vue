<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    nombre: '',
    apellido: '',
    documento: '',
    telefono: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function enviar() {
    form.post('/registro', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Crear cuenta" />
    <div
        class="bg-fondo flex min-h-screen items-center justify-center px-4 text-slate-200"
    >
        <form
            class="panel w-full max-w-md space-y-4 p-6"
            @submit.prevent="enviar"
        >
            <h1 class="text-xl font-bold text-white">Crear cuenta</h1>
            <p class="text-sm text-slate-400">
                Regístrate para ver tus facturas y pedir repuestos.
            </p>

            <div>
                <label class="mb-1 block text-sm">Nombre</label>
                <input v-model="form.nombre" type="text" class="campo w-full" />
                <p v-if="form.errors.nombre" class="mt-1 text-sm text-red-400">
                    {{ form.errors.nombre }}
                </p>
            </div>

            <div>
                <label class="mb-1 block text-sm">Apellido</label>
                <input
                    v-model="form.apellido"
                    type="text"
                    class="campo w-full"
                />
                <p
                    v-if="form.errors.apellido"
                    class="mt-1 text-sm text-red-400"
                >
                    {{ form.errors.apellido }}
                </p>
            </div>

            <div>
                <label class="mb-1 block text-sm">Documento de identidad</label>
                <input
                    v-model="form.documento"
                    type="text"
                    inputmode="numeric"
                    class="campo w-full"
                />
                <p
                    v-if="form.errors.documento"
                    class="mt-1 text-sm text-red-400"
                >
                    {{ form.errors.documento }}
                </p>
            </div>

            <div>
                <label class="mb-1 block text-sm">Teléfono</label>
                <input
                    v-model="form.telefono"
                    type="tel"
                    inputmode="tel"
                    class="campo w-full"
                />
                <p
                    v-if="form.errors.telefono"
                    class="mt-1 text-sm text-red-400"
                >
                    {{ form.errors.telefono }}
                </p>
            </div>

            <div>
                <label class="mb-1 block text-sm">Correo</label>
                <input v-model="form.email" type="email" class="campo w-full" />
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-400">
                    {{ form.errors.email }}
                </p>
            </div>

            <div>
                <label class="mb-1 block text-sm">Contraseña</label>
                <input
                    v-model="form.password"
                    type="password"
                    class="campo w-full"
                />
                <p
                    v-if="form.errors.password"
                    class="mt-1 text-sm text-red-400"
                >
                    {{ form.errors.password }}
                </p>
            </div>

            <div>
                <label class="mb-1 block text-sm">Confirmar contraseña</label>
                <input
                    v-model="form.password_confirmation"
                    type="password"
                    class="campo w-full"
                />
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full rounded-md bg-amber-500 px-4 py-2 font-semibold text-slate-900 hover:bg-amber-400 disabled:opacity-50"
            >
                Crear cuenta
            </button>

            <p class="text-center text-sm text-slate-400">
                ¿Ya tienes cuenta?
                <Link href="/login" class="text-amber-400 hover:underline"
                    >Inicia sesión</Link
                >
            </p>
        </form>
    </div>
</template>
