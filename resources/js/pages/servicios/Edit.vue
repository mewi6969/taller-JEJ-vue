<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    servicio: Object,
    motocicletas: Array,
    mecanicos: Array,
    repuestos: Array,
});

const form = useForm({
    motocicleta_id: props.servicio.motocicleta_id,
    mecanico_id: props.servicio.mecanico_id ?? '',
    descripcion_problema: props.servicio.descripcion_problema,
    estado: props.servicio.estado,
    costo_mano_obra: props.servicio.costo_mano_obra,
    fecha_ingreso: props.servicio.fecha_ingreso,
    fecha_entrega: props.servicio.fecha_entrega ?? '',
    observaciones: props.servicio.observaciones ?? '',
});

function submit() {
    form.put(`/servicios/${props.servicio.id}`);
}

const repuestoForm = useForm({
    repuesto_id: '',
    cantidad: 1,
});

function agregarRepuesto() {
    repuestoForm.post(`/servicios/${props.servicio.id}/repuestos`, {
        preserveScroll: true,
        onSuccess: () => repuestoForm.reset(),
    });
}

function quitarRepuesto(detalle) {
    if (confirm(`¿Quitar ${detalle.repuesto.nombre} de este servicio? El stock se devolverá automáticamente.`)) {
        router.delete(`/servicios/${props.servicio.id}/repuestos/${detalle.id}`, {
            preserveScroll: true,
        });
    }
}
</script>

<template>
    <Head title="Editar Servicio" />

    <AppLayout>
        <h1>Editar Servicio</h1>

        <form @submit.prevent="submit" class="form">
            <div class="field">
                <label>Motocicleta</label>
                <select v-model="form.motocicleta_id">
                    <option v-for="moto in motocicletas" :key="moto.id" :value="moto.id">
                        {{ moto.placa }} — {{ moto.cliente.nombre }} {{ moto.cliente.apellido }}
                    </option>
                </select>
                <span v-if="form.errors.motocicleta_id" class="error">{{ form.errors.motocicleta_id }}</span>
            </div>
            <div class="field">
                <label>Mecánico</label>
                <select v-model="form.mecanico_id">
                    <option value="">-- Sin asignar --</option>
                    <option v-for="mecanico in mecanicos" :key="mecanico.id" :value="mecanico.id">
                        {{ mecanico.name }}
                    </option>
                </select>
                <span v-if="form.errors.mecanico_id" class="error">{{ form.errors.mecanico_id }}</span>
            </div>
            <div class="field">
                <label>Descripción del problema</label>
                <textarea v-model="form.descripcion_problema" rows="4"></textarea>
                <span v-if="form.errors.descripcion_problema" class="error">{{ form.errors.descripcion_problema }}</span>
            </div>
            <div class="field">
                <label>Estado</label>
                <select v-model="form.estado">
                    <option value="pendiente">Pendiente</option>
                    <option value="en_proceso">En proceso</option>
                    <option value="terminado">Terminado</option>
                    <option value="entregado">Entregado</option>
                </select>
                <span v-if="form.errors.estado" class="error">{{ form.errors.estado }}</span>
            </div>
            <div class="field">
                <label>Costo de mano de obra</label>
                <input type="number" step="0.01" v-model="form.costo_mano_obra" />
                <span v-if="form.errors.costo_mano_obra" class="error">{{ form.errors.costo_mano_obra }}</span>
            </div>
            <div class="field">
                <label>Fecha de ingreso</label>
                <input type="date" v-model="form.fecha_ingreso" />
                <span v-if="form.errors.fecha_ingreso" class="error">{{ form.errors.fecha_ingreso }}</span>
            </div>
            <div class="field">
                <label>Fecha de entrega</label>
                <input type="date" v-model="form.fecha_entrega" />
                <span v-if="form.errors.fecha_entrega" class="error">{{ form.errors.fecha_entrega }}</span>
            </div>
            <div class="field">
                <label>Observaciones</label>
                <textarea v-model="form.observaciones" rows="3"></textarea>
                <span v-if="form.errors.observaciones" class="error">{{ form.errors.observaciones }}</span>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary" :disabled="form.processing">Actualizar</button>
                <Link href="/servicios" class="btn">Cancelar</Link>
            </div>
        </form>

        <hr class="divider" />

        <h2>Repuestos usados</h2>

        <table class="table">
            <thead>
                <tr>
                    <th>Repuesto</th>
                    <th>Cantidad</th>
                    <th>Precio unitario</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="detalle in servicio.detalles" :key="detalle.id">
                    <td>{{ detalle.repuesto.nombre }}</td>
                    <td>{{ detalle.cantidad }}</td>
                    <td>${{ Number(detalle.precio_unitario).toLocaleString('es-CO') }}</td>
                    <td>${{ (detalle.cantidad * detalle.precio_unitario).toLocaleString('es-CO') }}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" @click="quitarRepuesto(detalle)">Quitar</button>
                    </td>
                </tr>
                <tr v-if="servicio.detalles.length === 0">
                    <td colspan="5" class="empty">No se han agregado repuestos a este servicio.</td>
                </tr>
            </tbody>
        </table>

        <p class="costo-total">Costo total: <strong>${{ Number(servicio.costo_total).toLocaleString('es-CO') }}</strong></p>

        <form @submit.prevent="agregarRepuesto" class="add-repuesto-form">
            <select v-model="repuestoForm.repuesto_id">
                <option value="">-- Selecciona un repuesto --</option>
                <option v-for="repuesto in repuestos" :key="repuesto.id" :value="repuesto.id">
                    {{ repuesto.nombre }} (stock: {{ repuesto.cantidad }})
                </option>
            </select>
            <input type="number" min="1" v-model="repuestoForm.cantidad" placeholder="Cantidad" />
            <button type="submit" class="btn btn-primary" :disabled="repuestoForm.processing">Agregar</button>
        </form>
        <span v-if="repuestoForm.errors.repuesto_id" class="error">{{ repuestoForm.errors.repuesto_id }}</span>
        <span v-if="repuestoForm.errors.cantidad" class="error">{{ repuestoForm.errors.cantidad }}</span>
    </AppLayout>
</template>

<style scoped>
.form { max-width: 480px; display: flex; flex-direction: column; gap: 1rem; }
.field { display: flex; flex-direction: column; gap: 0.35rem; }
.field input, .field select, .field textarea { padding: 0.55rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #e2e8f0; font-family: inherit; }
.error { color: #f87171; font-size: 0.85rem; }
.actions { display: flex; gap: 0.75rem; margin-top: 0.5rem; }
.btn { display: inline-block; padding: 0.55rem 1rem; border-radius: 6px; border: 1px solid #475569; background: transparent; color: #e2e8f0; cursor: pointer; text-decoration: none; font-size: 0.9rem; }
.btn-primary { background: #3b82f6; border-color: #3b82f6; color: white; }
.btn-sm { padding: 0.3rem 0.6rem; font-size: 0.8rem; }
.btn-danger { border-color: #b91c1c; color: #fca5a5; }
.divider { border: none; border-top: 1px solid #334155; margin: 2rem 0; }
.table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; max-width: 640px; }
.table th, .table td { padding: 0.6rem; border-bottom: 1px solid #334155; text-align: left; }
.empty { text-align: center; color: #94a3b8; }
.costo-total { font-size: 1.05rem; margin-bottom: 1.5rem; }
.add-repuesto-form { display: flex; gap: 0.5rem; max-width: 480px; align-items: center; }
.add-repuesto-form select { flex: 2; padding: 0.55rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #e2e8f0; }
.add-repuesto-form input { flex: 1; padding: 0.55rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #e2e8f0; }
</style>
