<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: { type: String, default: null },
    variant: { type: String, default: 'default' }, // default | primary | danger
    size: { type: String, default: 'md' }, // md | sm
    type: { type: String, default: 'button' },
    disabled: { type: Boolean, default: false },
});

defineEmits(['click']);

const base =
    'inline-flex items-center justify-center rounded-lg border font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-fondo disabled:opacity-50 disabled:cursor-not-allowed';

const variantClasses = {
    default:
        'border-linea bg-transparent text-slate-100 hover:bg-superficie-alta focus:ring-slate-500',
    primary:
        'border-amber-600 bg-amber-600 text-white shadow-sm hover:border-amber-500 hover:bg-amber-500 focus:ring-amber-500',
    danger: 'border-red-700/70 text-red-300 hover:bg-red-900/30 focus:ring-red-500',
};

const sizeClasses = {
    md: 'px-4 py-2 text-sm',
    sm: 'px-3 py-1.5 text-xs',
};
</script>

<template>
    <Link
        v-if="href"
        :href="href"
        :class="[base, variantClasses[variant], sizeClasses[size]]"
    >
        <slot />
    </Link>
    <button
        v-else
        :type="type"
        :disabled="disabled"
        :class="[base, variantClasses[variant], sizeClasses[size]]"
        @click="$emit('click', $event)"
    >
        <slot />
    </button>
</template>
