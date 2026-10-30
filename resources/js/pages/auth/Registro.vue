<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const form = useForm({
    nombre: '',
    apellido: '',
    documento: '',
    telefono: '',
    email: '',
    password: '',
    password_confirmation: '',
});

// Mostrar u ocultar cada contraseña por separado
const verPassword = ref(false);
const verConfirmacion = ref(false);

const dominios = [
    'gmail.com',
    'hotmail.com',
    'outlook.com',
    'yahoo.com',
    'icloud.com',
];

// Sugiere dominios completos mientras se escribe después del @
const sugerencias = computed(() => {
    const valor = form.email ?? '';
    const arroba = valor.indexOf('@');

    if (arroba < 1) return [];

    const usuario = valor.slice(0, arroba);
    const parcial = valor.slice(arroba + 1).toLowerCase();

    return dominios
        .filter((d) => d.startsWith(parcial) && d !== parcial)
        .map((d) => `${usuario}@${d}`);
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
                <input
                    v-model="form.email"
                    type="email"
                    autocomplete="email"
                    placeholder="nombre@gmail.com"
                    pattern=".+@.+\..{2,}"
                    title="Escribe el correo completo, por ejemplo nombre@gmail.com"
                    class="campo w-full"
                />
                <div
                    v-if="sugerencias.length"
                    class="mt-2 flex flex-wrap gap-2"
                >
                    <button
                        v-for="s in sugerencias"
                        :key="s"
                        type="button"
                        class="border-linea hover:bg-superficie-alta rounded-md border px-3 py-1 text-xs text-slate-300 transition-colors"
                        @click="form.email = s"
                    >
                        {{ s }}
                    </button>
                </div>
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-400">
                    {{ form.errors.email }}
                </p>
            </div>

            <!-- Contraseña -->
            <div>
                <label class="mb-1 block text-sm">Contraseña</label>
                <div class="relative">
                    <input
                        v-model="form.password"
                        :type="verPassword ? 'text' : 'password'"
                        autocomplete="new-password"
                        class="campo w-full pr-11"
                    />
                    <button
                        type="button"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 transition-colors hover:text-slate-100"
                        :aria-label="
                            verPassword
                                ? 'Ocultar contraseña'
                                : 'Mostrar contraseña'
                        "
                        :aria-pressed="verPassword"
                        @click="verPassword = !verPassword"
                    >
                        <!-- Ojo abierto: la contraseña está visible -->
                        <svg
                            v-if="verPassword"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="h-5 w-5"
                        >
                            <path
                                d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"
                            />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <!-- Ojo tachado: la contraseña está oculta -->
                        <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="h-5 w-5"
                        >
                            <path
                                d="M17.94 17.94A10.4 10.4 0 0 1 12 19c-6.5 0-10-7-10-7a18.5 18.5 0 0 1 5.06-5.94"
                            />
                            <path
                                d="M9.9 4.24A9.1 9.1 0 0 1 12 5c6.5 0 10 7 10 7a18.5 18.5 0 0 1-2.16 3.19"
                            />
                            <path d="M1 1l22 22" />
                        </svg>
                    </button>
                </div>
                <p
                    v-if="form.errors.password"
                    class="mt-1 text-sm text-red-400"
                >
                    {{ form.errors.password }}
                </p>
            </div>

            <!-- Confirmar contraseña -->
            <div>
                <label class="mb-1 block text-sm">Confirmar contraseña</label>
                <div class="relative">
                    <input
                        v-model="form.password_confirmation"
                        :type="verConfirmacion ? 'text' : 'password'"
                        autocomplete="new-password"
                        class="campo w-full pr-11"
                    />
                    <button
                        type="button"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 transition-colors hover:text-slate-100"
                        :aria-label="
                            verConfirmacion
                                ? 'Ocultar confirmación'
                                : 'Mostrar confirmación'
                        "
                        :aria-pressed="verConfirmacion"
                        @click="verConfirmacion = !verConfirmacion"
                    >
                        <svg
                            v-if="verConfirmacion"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="h-5 w-5"
                        >
                            <path
                                d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"
                            />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="h-5 w-5"
                        >
                            <path
                                d="M17.94 17.94A10.4 10.4 0 0 1 12 19c-6.5 0-10-7-10-7a18.5 18.5 0 0 1 5.06-5.94"
                            />
                            <path
                                d="M9.9 4.24A9.1 9.1 0 0 1 12 5c6.5 0 10 7 10 7a18.5 18.5 0 0 1-2.16 3.19"
                            />
                            <path d="M1 1l22 22" />
                        </svg>
                    </button>
                </div>
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
