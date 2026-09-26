<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Iniciar sesión" />

    <div class="login-page">
        <div class="login-card">
            <h1>Taller de Motos</h1>

            <form @submit.prevent="submit">
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" type="email" v-model="form.email" autofocus autocomplete="username" />
                    <span v-if="form.errors.email" class="error">{{ form.errors.email }}</span>
                </div>

                <div class="field">
                    <label for="password">Contraseña</label>
                    <input id="password" type="password" v-model="form.password" autocomplete="current-password" />
                    <span v-if="form.errors.password" class="error">{{ form.errors.password }}</span>
                </div>

                <label class="remember">
                    <input type="checkbox" v-model="form.remember" />
                    Recordarme
                </label>

                <button type="submit" :disabled="form.processing">Iniciar sesión</button>
            </form>
        </div>
    </div>
</template>

<style scoped>
.login-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #0f172a;
}
.login-card {
    background: #1e293b;
    padding: 2.5rem;
    border-radius: 12px;
    width: 360px;
    color: #e2e8f0;
}
.field {
    margin-bottom: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}
input[type="email"],
input[type="password"] {
    padding: 0.6rem;
    border-radius: 6px;
    border: 1px solid #334155;
    background: #0f172a;
    color: #e2e8f0;
}
.error {
    color: #f87171;
    font-size: 0.85rem;
}
.remember {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.25rem;
    font-size: 0.9rem;
}
button {
    width: 100%;
    padding: 0.7rem;
    border-radius: 6px;
    border: none;
    background: #3b82f6;
    color: white;
    font-weight: 600;
    cursor: pointer;
}
button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
