<template>
  <div class="flex items-center space-x-2">
    <span
      v-for="star in 5"
      :key="star"
      class="cursor-pointer text-2xl"
      :class="{ 'text-gold': star <= hoverRating, 'text-gray-400': star > hoverRating }"
      @mouseover="hoverRating = star"
      @mouseleave="hoverRating = modelValue"
      @click="setRating(star)"
    >
      ★
    </span>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch } from "vue";

const props = defineProps({
  modelValue: { type: Number, default: 0 }, // v-model binding
});

const emit = defineEmits(["update:modelValue"]);
const hoverRating = ref(props.modelValue);

watch(() => props.modelValue, (newValue) => {
  hoverRating.value = newValue;
});

const setRating = (value) => {
  emit("update:modelValue", value);
};
</script>

<style scoped>
.text-gold {
  color: gold;
}
</style>