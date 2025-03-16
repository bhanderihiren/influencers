<script setup>
import { ref, onMounted } from 'vue';

defineProps({
    modelValue: String, // The selected value
    options: Array, // The list of available options
});

defineEmits(['update:modelValue']);

const select = ref(null);

onMounted(() => {
    if (select.value.hasAttribute('autofocus')) {
        select.value.focus();
    }
});

defineExpose({ focus: () => select.value.focus() });
</script>

<template>
    <select
        ref="select"
        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 
               focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 
               dark:focus:ring-indigo-600 rounded-md shadow-sm w-full p-2"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
    >
        <option value="" disabled>Select a role</option>
        <option v-for="option in options" :key="option" :value="option">
            {{ option }}
        </option>
    </select>
</template>