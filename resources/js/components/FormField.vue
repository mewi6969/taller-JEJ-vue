<script setup>
defineProps({
    label: { type: String, required: true },
    error: { type: String, default: null },
    ayuda: { type: String, default: null },
    requerido: { type: Boolean, default: false },
});
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <label class="text-sm font-medium text-slate-200">
            {{ label }}
            <span v-if="requerido" class="text-amber-400">*</span>
        </label>

        <slot />

        <p v-if="error" class="flex items-center gap-1.5 text-xs text-red-400">
            <span
                class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-red-500/20 text-[10px] font-bold"
            >
                !
            </span>
            {{ error }}
        </p>
        <p v-else-if="ayuda" class="text-xs text-slate-500">{{ ayuda }}</p>
    </div>
</template>
