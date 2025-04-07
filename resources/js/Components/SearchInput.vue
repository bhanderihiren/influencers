<template>
  <div class="search-input relative flex-1 md:w-64">
    <input
      type="text"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :placeholder="placeholder"
      class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
    >
    <SearchIcon class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
  </div>
</template>

<script setup>
defineProps(['modelValue', 'placeholder']);
defineEmits(['update:modelValue']);
import { SearchIcon } from 'lucide-vue-next'; // Or whatever icon source you’re using
</script>
