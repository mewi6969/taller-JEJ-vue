<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    motocicleta: Object,
    clientes: Array,
});

const form = useForm({
    cliente_id: props.motocicleta.cliente_id,
    placa: props.motocicleta.placa,
    marca: props.motocicleta.marca,
    modelo: props.motocicleta.modelo,
    anio: props.motocicleta.anio,
    cilindraje: props.motocicleta.cilindraje,
    color: props.motocicleta.color,
});

function submit() {
    form.put(`/motocicletas/${props.motocicleta.id}`);
}
</script>

<template>
    <Head title="Editar Motocicleta" />

    <AppLayout>
        <h1>Editar Motocicleta</h1>

        <form @submit.prevent="submit" class="form">
            <div class="field">
                <label>Cliente</label>
                <select v-model="form.cliente_id">
                    <option
                        v-for="cliente in clientes"
                        :key="cliente.id"
                        :value="cliente.id"
                    >
                        {{ cliente.nombre }} {{ cliente.apellido }} —
                        {{ cliente.documento }}
                    </option>
                </select>
                <span v-if="form.errors.cliente_id" class="error">{{
                    form.errors.cliente_id
                }}</span>
            </div>
            <div class="field">
                <label>Placa</label>
                <input type="text" v-model="form.placa" />
                <span v-if="form.errors.placa" class="error">{{
                    form.errors.placa
                }}</span>
            </div>
            <div class="field">
                <label>Marca</label>
                <input type="text" v-model="form.marca" />
                <span v-if="form.errors.marca" class="error">{{
                    form.errors.marca
                }}</span>
            </div>
            <div class="field">
                <label>Modelo</label>
                <input type="text" v-model="form.modelo" />
                <span v-if="form.errors.modelo" class="error">{{
                    form.errors.modelo
                }}</span>
            </div>
            <div class="field">
                <label>Año</label>
                <input type="number" v-model="form.anio" />
                <span v-if="form.errors.anio" class="error">{{
                    form.errors.anio
                }}</span>
            </div>
            <div class="field">
                <label>Cilindraje</label>
                <input type="number" v-model="form.cilindraje" />
                <span v-if="form.errors.cilindraje" class="error">{{
                    form.errors.cilindraje
                }}</span>
            </div>
            <div class="field">
                <label>Color</label>
                <input type="text" v-model="form.color" />
                <span v-if="form.errors.color" class="error">{{
                    form.errors.color
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
                <Link href="/motocicletas" class="btn">Cancelar</Link>
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
