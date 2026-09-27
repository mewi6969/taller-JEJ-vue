<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    repuesto: Object,
});

const form = useForm({
    nombre: props.repuesto.nombre,
    descripcion: props.repuesto.descripcion,
    precio: props.repuesto.precio,
    cantidad: props.repuesto.cantidad,
    cantidad_minima: props.repuesto.cantidad_minima,
});

function submit() {
    form.put(`/repuestos/${props.repuesto.id}`);
}
</script>

<template>
    <Head title="Editar Repuesto" />

    <AppLayout>
        <h1>Editar Repuesto</h1>

        <form @submit.prevent="submit" class="form">
            <div class="field">
                <label>Nombre</label>
                <input type="text" v-model="form.nombre" />
                <span v-if="form.errors.nombre" class="error">{{
                    form.errors.nombre
                }}</span>
            </div>
            <div class="field">
                <label>Descripción</label>
                <input type="text" v-model="form.descripcion" />
                <span v-if="form.errors.descripcion" class="error">{{
                    form.errors.descripcion
                }}</span>
            </div>
            <div class="field">
                <label>Precio</label>
                <input type="number" step="0.01" v-model="form.precio" />
                <span v-if="form.errors.precio" class="error">{{
                    form.errors.precio
                }}</span>
            </div>
            <div class="field">
                <label>Cantidad</label>
                <input type="number" v-model="form.cantidad" />
                <span v-if="form.errors.cantidad" class="error">{{
                    form.errors.cantidad
                }}</span>
            </div>
            <div class="field">
                <label>Cantidad mínima</label>
                <input type="number" v-model="form.cantidad_minima" />
                <span v-if="form.errors.cantidad_minima" class="error">{{
                    form.errors.cantidad_minima
                }}</span>
            </div>

            <div class="actions">
                <button
                    type="submit"
                    class="btn btn-primary"
                    :disabled="form.processing"
                >
                    Actualizar
                </button>
                <Link href="/repuestos" class="btn">Cancelar</Link>
            </div>
        </form>
    </AppLayout>
</template>

<style scoped>
.form {
    max-width: 420px;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.field {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}
.field input,
.field select {
    padding: 0.55rem;
    border-radius: 6px;
    border: 1px solid #334155;
    background: #0f172a;
    color: #e2e8f0;
}
.error {
    color: #f87171;
    font-size: 0.85rem;
}
.actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 0.5rem;
}
.btn {
    display: inline-block;
    padding: 0.55rem 1rem;
    border-radius: 6px;
    border: 1px solid #475569;
    background: transparent;
    color: #e2e8f0;
    cursor: pointer;
    text-decoration: none;
    font-size: 0.9rem;
}
.btn-primary {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
}
</style>
