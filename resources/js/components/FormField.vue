<script setup>
defineProps({
    label: { type: String, required: true },
    error: { type: String, default: null },
});
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <label class="text-sm font-medium text-slate-300">{{ label }}</label>
        <slot />
        <span v-if="error" class="text-sm text-red-400">{{ error }}</span>
    </div>
</template>
