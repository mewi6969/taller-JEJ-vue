<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    servicios: Array,
});

const form = useForm({
    servicio_id: '',
    descuento: 0,
});

function submit() {
    form.post('/facturas');
}
</script>

<template>
    <Head title="Nueva Factura" />

    <AppLayout>
        <h1>Nueva Factura</h1>

        <form @submit.prevent="submit" class="form">
            <div class="field">
                <label>Servicio</label>
                <select v-model="form.servicio_id">
                    <option value="" disabled>Selecciona un servicio</option>
                    <option
                        v-for="servicio in servicios"
                        :key="servicio.id"
                        :value="servicio.id"
                    >
                        {{ servicio.motocicleta.cliente.nombre }}
                        {{ servicio.motocicleta.cliente.apellido }} -
                        {{ servicio.motocicleta.placa }} - ${{
                            Number(servicio.costo_total).toLocaleString('es-CO')
                        }}
                    </option>
                </select>
                <span v-if="form.errors.servicio_id" class="error">{{
                    form.errors.servicio_id
                }}</span>
                <span v-if="servicios.length === 0" class="hint">
                    No hay servicios terminados pendientes de facturar.
                </span>
            </div>
            <div class="field">
                <label>Descuento (opcional)</label>
                <input type="number" step="0.01" v-model="form.descuento" />
                <span v-if="form.errors.descuento" class="error">{{
                    form.errors.descuento
                }}</span>
            </div>

            <div class="actions">
                <button
                    type="submit"
                    class="btn btn-primary"
                    :disabled="form.processing || servicios.length === 0"
                >
                    Generar factura
                </button>
                <Link href="/facturas" class="btn">Cancelar</Link>
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
.hint {
    color: #94a3b8;
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
