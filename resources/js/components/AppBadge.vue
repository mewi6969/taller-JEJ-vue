<script setup>
defineProps({
    variant: { type: String, default: 'neutral' }, // ok | danger | warning | neutral
});

const variantClasses = {
    ok: 'bg-green-500/15 text-green-400',
    danger: 'bg-red-700/15 text-red-300',
    warning: 'bg-amber-500/15 text-amber-400',
    info: 'bg-blue-500/15 text-blue-400',
    neutral: 'bg-slate-500/15 text-slate-300',
};
</script>

<template>
    <span
        class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize"
        :class="variantClasses[variant]"
    >
        <slot />
    </span>
</template>
