<script setup>
defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: '¿Estás seguro?' },
    message: { type: String, default: '' },
});

defineEmits(['confirm', 'cancel']);
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
    >
        <div
            class="w-full max-w-sm rounded-lg border border-slate-700 bg-slate-900 p-6 shadow-xl"
        >
            <h2 class="mb-2 text-lg font-semibold text-slate-100">
                {{ title }}
            </h2>
            <p class="mb-6 text-sm text-slate-400">{{ message }}</p>
            <div class="flex justify-end gap-3">
                <button
                    class="rounded-md border border-slate-600 px-4 py-2 text-sm text-slate-200 hover:bg-slate-800"
                    @click="$emit('cancel')"
                >
                    Cancelar
                </button>
                <button
                    class="rounded-md border border-red-700 bg-red-700 px-4 py-2 text-sm text-white hover:bg-red-800"
                    @click="$emit('confirm')"
                >
                    Eliminar
                </button>
            </div>
        </div>
    </div>
</template>
