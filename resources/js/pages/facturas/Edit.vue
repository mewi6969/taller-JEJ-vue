<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    factura: Object,
});

const form = useForm({
    estado: props.factura.estado,
    metodo_pago: props.factura.metodo_pago || '',
    fecha_pago: props.factura.fecha_pago
        ? props.factura.fecha_pago.split('T')[0]
        : '',
});

const requierePago = computed(() => form.estado === 'pagada');

function submit() {
    form.put(`/facturas/${props.factura.id}`);
}
</script>

<template>
    <Head title="Editar Factura" />

    <AppLayout>
        <h1>Factura {{ factura.numero_factura }}</h1>

        <p class="info">
            Cliente: {{ factura.servicio.motocicleta.cliente.nombre }}
            {{ factura.servicio.motocicleta.cliente.apellido }}<br />
            Motocicleta: {{ factura.servicio.motocicleta.placa }}<br />
            Total: ${{ Number(factura.total).toLocaleString('es-CO') }}
        </p>

        <form @submit.prevent="submit" class="form">
            <div class="field">
                <label>Estado</label>
                <select v-model="form.estado">
                    <option value="pendiente">Pendiente</option>
                    <option value="pagada">Pagada</option>
                    <option value="anulada">Anulada</option>
                </select>
                <span v-if="form.errors.estado" class="error">{{
                    form.errors.estado
                }}</span>
            </div>

            <div class="field" v-if="requierePago">
                <label>Método de pago</label>
                <select v-model="form.metodo_pago">
                    <option value="" disabled>Selecciona un método</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="tarjeta">Tarjeta</option>
                </select>
                <span v-if="form.errors.metodo_pago" class="error">{{
                    form.errors.metodo_pago
                }}</span>
            </div>

            <div class="field" v-if="requierePago">
                <label>Fecha de pago</label>
                <input type="date" v-model="form.fecha_pago" />
                <span v-if="form.errors.fecha_pago" class="error">{{
                    form.errors.fecha_pago
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
                <Link href="/facturas" class="btn">Cancelar</Link>
            </div>
        </form>
    </AppLayout>
</template>

<style scoped>
.info {
    margin-bottom: 1.5rem;
    color: #cbd5e1;
    line-height: 1.6;
}
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
