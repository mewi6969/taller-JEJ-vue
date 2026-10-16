<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({ repuestos: Array });

const cantidades = ref({});
const form = useForm({ nota: '' });

const items = computed(() =>
    Object.entries(cantidades.value)
        .filter(([, cantidad]) => Number(cantidad) > 0)
        .map(([id, cantidad]) => ({
            repuesto_id: Number(id),
            cantidad: Number(cantidad),
        })),
);

const total = computed(() =>
    items.value.reduce((suma, item) => {
        const repuesto = props.repuestos.find((r) => r.id === item.repuesto_id);

        return suma + (repuesto ? Number(repuesto.precio) * item.cantidad : 0);
    }, 0),
);

function dinero(valor) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0,
    }).format(valor);
}

function enviar() {
    form.transform((datos) => ({ ...datos, items: items.value })).post(
        '/portal/pedidos',
        {
            onSuccess: () => {
                cantidades.value = {};
                form.reset();
            },
        },
    );
}
</script>

<template>
    <AppLayout>
        <Head title="Catálogo de repuestos" />
        <h1 class="mb-2 text-2xl font-bold text-white">
            Catálogo de repuestos
        </h1>
        <p class="mb-6 text-sm text-slate-400">
            Elige las cantidades y envía tu pedido. El taller lo revisará y te
            contactará.
        </p>

        <div v-if="repuestos.length === 0" class="panel p-6 text-slate-400">
            Por ahora no hay repuestos disponibles.
        </div>

        <form v-else class="space-y-6" @submit.prevent="enviar">
            <div class="panel overflow-x-auto">
                <table class="tabla w-full">
                    <thead>
                        <tr>
                            <th>Repuesto</th>
                            <th>Precio</th>
                            <th>Disponibles</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="r in repuestos" :key="r.id">
                            <td>{{ r.nombre }}</td>
                            <td>{{ dinero(r.precio) }}</td>
                            <td>{{ r.cantidad }}</td>
                            <td>
                                <input
                                    v-model.number="cantidades[r.id]"
                                    type="number"
                                    min="0"
                                    :max="r.cantidad"
                                    placeholder="0"
                                    class="campo w-24"
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="panel space-y-4 p-6">
                <div>
                    <label class="mb-1 block text-sm">Nota (opcional)</label>
                    <textarea
                        v-model="form.nota"
                        rows="2"
                        maxlength="500"
                        class="campo w-full"
                        placeholder="Ej: es para mi moto de placa ABC12D"
                    />
                </div>

                <p
                    v-for="(mensaje, campo) in form.errors"
                    :key="campo"
                    class="text-sm text-red-400"
                >
                    {{ mensaje }}
                </p>

                <div class="flex items-center justify-between">
                    <p class="text-white">
                        Total estimado:
                        <strong>{{ dinero(total) }}</strong>
                    </p>
                    <button
                        type="submit"
                        :disabled="form.processing || items.length === 0"
                        class="rounded-md bg-amber-500 px-4 py-2 font-semibold text-slate-900 hover:bg-amber-400 disabled:opacity-50"
                    >
                        Enviar pedido
                    </button>
                </div>
            </div>
        </form>
    </AppLayout>
</template>
