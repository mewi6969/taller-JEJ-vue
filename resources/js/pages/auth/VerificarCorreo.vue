<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';

defineProps({ email: String });

const form = useForm({ codigo: '' });

function verificar() {
    form.post('/verificar-correo');
}

function reenviar() {
    router.post('/verificar-correo/reenviar');
}
</script>

<template>
    <AppLayout>
        <Head title="Verificar correo" />
        <div class="panel mx-auto max-w-md space-y-4 p-6">
            <h1 class="text-xl font-bold text-white">Verifica tu correo</h1>
            <p class="text-sm text-slate-400">
                Enviamos un código de 6 dígitos a <strong>{{ email }}</strong
                >. Vence en 15 minutos.
            </p>

            <form class="space-y-3" @submit.prevent="verificar">
                <input
                    v-model="form.codigo"
                    type="text"
                    inputmode="numeric"
                    maxlength="6"
                    placeholder="000000"
                    class="campo w-full text-center text-lg tracking-widest"
                />
                <p v-if="form.errors.codigo" class="text-sm text-red-400">
                    {{ form.errors.codigo }}
                </p>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-md bg-amber-500 px-4 py-2 font-semibold text-slate-900 hover:bg-amber-400 disabled:opacity-50"
                >
                    Verificar
                </button>
            </form>

            <button
                type="button"
                class="text-sm text-amber-400 hover:underline"
                @click="reenviar"
            >
                Reenviar código
            </button>
        </div>
    </AppLayout>
</template>
