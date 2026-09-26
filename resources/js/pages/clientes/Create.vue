<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '../../layouts/AppLayout.vue';

const form = useForm({
    nombre: '',
    apellido: '',
    documento: '',
    telefono: '',
    email: '',
    direccion: '',
});

function submit() {
    form.post('/clientes');
}
</script>

<template>
    <Head title="Nuevo Cliente" />

    <AppLayout>
        <h1>Nuevo Cliente</h1>

        <form @submit.prevent="submit" class="form">
            <div class="field">
                <label>Nombre</label>
                <input type="text" v-model="form.nombre" />
                <span v-if="form.errors.nombre" class="error">{{ form.errors.nombre }}</span>
            </div>
            <div class="field">
                <label>Apellido</label>
                <input type="text" v-model="form.apellido" />
                <span v-if="form.errors.apellido" class="error">{{ form.errors.apellido }}</span>
            </div>
            <div class="field">
                <label>Documento</label>
                <input type="text" v-model="form.documento" />
                <span v-if="form.errors.documento" class="error">{{ form.errors.documento }}</span>
            </div>
            <div class="field">
                <label>Teléfono</label>
                <input type="text" v-model="form.telefono" />
                <span v-if="form.errors.telefono" class="error">{{ form.errors.telefono }}</span>
            </div>
            <div class="field">
                <label>Email</label>
                <input type="email" v-model="form.email" />
                <span v-if="form.errors.email" class="error">{{ form.errors.email }}</span>
            </div>
            <div class="field">
                <label>Dirección</label>
                <input type="text" v-model="form.direccion" />
                <span v-if="form.errors.direccion" class="error">{{ form.errors.direccion }}</span>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary" :disabled="form.processing">Guardar</button>
                <Link href="/clientes" class="btn">Cancelar</Link>
            </div>
        </form>
    </AppLayout>
</template>

<style scoped>
.form { max-width: 420px; display: flex; flex-direction: column; gap: 1rem; }
.field { display: flex; flex-direction: column; gap: 0.35rem; }
.field input { padding: 0.55rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #e2e8f0; }
.error { color: #f87171; font-size: 0.85rem; }
.actions { display: flex; gap: 0.75rem; margin-top: 0.5rem; }
.btn { display: inline-block; padding: 0.55rem 1rem; border-radius: 6px; border: 1px solid #475569; background: transparent; color: #e2e8f0; cursor: pointer; text-decoration: none; font-size: 0.9rem; }
.btn-primary { background: #3b82f6; border-color: #3b82f6; color: white; }
</style>
