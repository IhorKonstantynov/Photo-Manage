<script setup>
import { computed } from 'vue';

const props = defineProps({
    disabled: Boolean
});

const classes = computed(() => 
    props.disabled 
    ? 'cursor-not-allowed inline-flex items-center px-4 py-2 bg-gray-500 text-white border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150'
    : 'border-0 cursor-pointer inline-flex items-center px-4 py-2 bg-primary text-white border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150'
);

</script>

<template>
    <button
        :class="classes"
        :disabled="disabled"
    >
        <slot />
    </button>
</template>
