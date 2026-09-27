<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    motocicletas: Array,
    mecanicos: Array,
});

const form = useForm({
    motocicleta_id: '',
    mecanico_id: '',
    descripcion_problema: '',
    costo_mano_obra: '',
    fecha_ingreso: '',
});

function submit() {
    form.post('/servicios');
}
</script>

<template>
    <Head title="Nuevo Servicio" />

    <AppLayout>
        <h1>Nuevo Servicio</h1>

        <form @submit.prevent="submit" class="form">
            <div class="field">
                <label>Motocicleta</label>
                <select v-model="form.motocicleta_id">
                    <option value="">-- Selecciona una motocicleta --</option>
                    <option
                        v-for="moto in motocicletas"
                        :key="moto.id"
                        :value="moto.id"
                    >
                        {{ moto.placa }} — {{ moto.cliente.nombre }}
                        {{ moto.cliente.apellido }}
                    </option>
                </select>
                <span v-if="form.errors.motocicleta_id" class="error">{{
                    form.errors.motocicleta_id
                }}</span>
            </div>
            <div class="field">
                <label>Mecánico (opcional)</label>
                <select v-model="form.mecanico_id">
                    <option value="">-- Sin asignar --</option>
                    <option
                        v-for="mecanico in mecanicos"
                        :key="mecanico.id"
                        :value="mecanico.id"
                    >
                        {{ mecanico.name }}
                    </option>
                </select>
                <span v-if="form.errors.mecanico_id" class="error">{{
                    form.errors.mecanico_id
                }}</span>
            </div>
            <div class="field">
                <label>Descripción del problema</label>
                <textarea
                    v-model="form.descripcion_problema"
                    rows="4"
                ></textarea>
                <span v-if="form.errors.descripcion_problema" class="error">{{
                    form.errors.descripcion_problema
                }}</span>
            </div>
            <div class="field">
                <label>Costo de mano de obra</label>
                <input
                    type="number"
                    step="0.01"
                    v-model="form.costo_mano_obra"
                />
                <span v-if="form.errors.costo_mano_obra" class="error">{{
                    form.errors.costo_mano_obra
                }}</span>
            </div>
            <div class="field">
                <label>Fecha de ingreso</label>
                <input type="date" v-model="form.fecha_ingreso" />
                <span v-if="form.errors.fecha_ingreso" class="error">{{
                    form.errors.fecha_ingreso
                }}</span>
            </div>

            <div class="actions">
                <button
                    type="submit"
                    class="btn btn-primary"
                    :disabled="form.processing"
                >
                    Guardar
                </button>
                <Link href="/servicios" class="btn">Cancelar</Link>
            </div>
        </form>
    </AppLayout>
</template>

<style scoped>
.form {
    max-width: 480px;
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
.field select,
.field textarea {
    padding: 0.55rem;
    border-radius: 6px;
    border: 1px solid #334155;
    background: #0f172a;
    color: #e2e8f0;
    font-family: inherit;
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
