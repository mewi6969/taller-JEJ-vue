<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '../../layouts/AppLayout.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    rol: '',
    puede_crear_servicios: false,
});

function submit() {
    form.post('/usuarios');
}
</script>

<template>
    <Head title="Nuevo Usuario" />

    <AppLayout>
        <h1>Nuevo Usuario</h1>

        <form @submit.prevent="submit" class="form">
            <div class="field">
                <label>Nombre</label>
                <input type="text" v-model="form.name" />
                <span v-if="form.errors.name" class="error">{{ form.errors.name }}</span>
            </div>
            <div class="field">
                <label>Email</label>
                <input type="email" v-model="form.email" />
                <span v-if="form.errors.email" class="error">{{ form.errors.email }}</span>
            </div>
            <div class="field">
                <label>Contraseña</label>
                <input type="password" v-model="form.password" />
                <span v-if="form.errors.password" class="error">{{ form.errors.password }}</span>
            </div>
            <div class="field">
                <label>Confirmar contraseña</label>
                <input type="password" v-model="form.password_confirmation" />
            </div>
            <div class="field">
                <label>Rol</label>
                <select v-model="form.rol">
                    <option value="">-- Selecciona un rol --</option>
                    <option value="admin">Administrador</option>
                    <option value="recepcionista">Recepcionista</option>
                    <option value="mecanico">Mecánico</option>
                </select>
                <span v-if="form.errors.rol" class="error">{{ form.errors.rol }}</span>
            </div>
            <div class="field field-checkbox" v-if="form.rol === 'mecanico'">
                <label>
                    <input type="checkbox" v-model="form.puede_crear_servicios" />
                    Puede crear servicios (mecánico con permiso especial)
                </label>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary" :disabled="form.processing">Guardar</button>
                <Link href="/usuarios" class="btn">Cancelar</Link>
            </div>
        </form>
    </AppLayout>
</template>

<style scoped>
.form { max-width: 420px; display: flex; flex-direction: column; gap: 1rem; }
.field { display: flex; flex-direction: column; gap: 0.35rem; }
.field input, .field select { padding: 0.55rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #e2e8f0; }
.field-checkbox label { display: flex; align-items: center; gap: 0.5rem; flex-direction: row; }
.field-checkbox input { width: auto; }
.error { color: #f87171; font-size: 0.85rem; }
.actions { display: flex; gap: 0.75rem; margin-top: 0.5rem; }
.btn { display: inline-block; padding: 0.55rem 1rem; border-radius: 6px; border: 1px solid #475569; background: transparent; color: #e2e8f0; cursor: pointer; text-decoration: none; font-size: 0.9rem; }
.btn-primary { background: #3b82f6; border-color: #3b82f6; color: white; }
</style>
