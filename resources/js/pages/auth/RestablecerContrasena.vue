<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

import AppButton from '../../components/AppButton.vue';

const props = defineProps({
    token: { type: String, required: true },
    email: { type: String, default: '' },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post('/restablecer-contrasena', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Nueva contraseña" />

    <div
        class="bg-fondo flex min-h-screen flex-col items-center justify-center bg-[radial-gradient(ellipse_at_top,rgba(245,158,11,0.14),transparent_60%)] px-4 py-10 text-slate-200"
    >
        <Link
            href="/"
            class="mb-8 flex flex-col items-center gap-3 text-lg font-bold tracking-tight text-white"
        >
            <img
                src="/images/logo/logo-jej.png"
                alt="Logo de Taller JEJ"
                class="h-28 w-28 rounded-2xl"
            />
            Taller JEJ
        </Link>

        <div class="panel w-full max-w-sm">
            <div class="p-8">
                <h1 class="text-2xl font-semibold text-slate-50">
                    Nueva contraseña
                </h1>
                <p class="mt-1 text-sm text-slate-400">
                    Elige una contraseña nueva para tu cuenta.
                </p>

                <form @submit.prevent="submit" class="mt-8 flex flex-col gap-5">
                    <div class="flex flex-col gap-1.5">
                        <label
                            for="email"
                            class="text-sm font-medium text-slate-200"
                        >
                            Email
                        </label>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            class="campo"
                            autocomplete="username"
                        />
                        <p
                            v-if="form.errors.email"
                            class="flex items-center gap-1.5 text-xs text-red-400"
                        >
                            <span
                                class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-red-500/20 text-[10px] font-bold"
                            >
                                !
                            </span>
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label
                            for="password"
                            class="text-sm font-medium text-slate-200"
                        >
                            Contraseña nueva
                        </label>
                        <input
                            id="password"
                            type="password"
                            v-model="form.password"
                            class="campo"
                            autofocus
                            autocomplete="new-password"
                        />
                        <p
                            v-if="form.errors.password"
                            class="flex items-center gap-1.5 text-xs text-red-400"
                        >
                            <span
                                class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-red-500/20 text-[10px] font-bold"
                            >
                                !
                            </span>
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label
                            for="password_confirmation"
                            class="text-sm font-medium text-slate-200"
                        >
                            Confirmar contraseña
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            v-model="form.password_confirmation"
                            class="campo"
                            autocomplete="new-password"
                        />
                    </div>

                    <AppButton
                        type="submit"
                        variant="primary"
                        class="w-full"
                        :disabled="form.processing"
                    >
                        {{
                            form.processing
                                ? 'Guardando...'
                                : 'Cambiar contraseña'
                        }}
                    </AppButton>
                </form>
            </div>
        </div>
    </div>
</template>
