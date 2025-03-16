<template>
  <div class="p-4">
    <h2 class="text-xl font-bold mb-2">Select up to 3 Categories</h2>

    <div class="flex flex-wrap gap-2">
      <button
        v-for="category in categories"
        :key="category.id"
        @click="toggleCategory(category)"
        :class="{
          'bg-blue-500 text-white': selectedCategories.some(c => c.id === category.id),
          'bg-gray-200': !selectedCategories.some(c => c.id === category.id),
          'cursor-not-allowed': selectedCategories.length >= 3 && !selectedCategories.some(c => c.id === category.id)
        }"
        class="px-3 py-1 rounded-md"
      >
        {{ category.name }}
      </button>
    </div>

    <p class="mt-4">
      Selected:
      <span v-if="selectedCategories.length > 0">
        {{ selectedCategories.map(c => c.name).join(', ') }}
      </span>
      <span v-else>None</span>
    </p>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from "vue";
import axios from "axios";

const props = defineProps({
  selected: {
    type: Array,
    default: () => [],
  }
});

const emit = defineEmits(["update:selected"]);

const categories = ref([]);
const selectedCategories = ref([]); // Will be populated later

// Fetch categories from API
const fetchCategories = async () => {
  try {
    const response = await axios.get("/api/categories");
    categories.value = response.data;

    // Ensure selected categories are matched correctly
    selectedCategories.value = response.data.filter(category =>
      props.selected.some(selected => selected?.id === category.id)
    );
  } catch (error) {
    console.error("Error fetching categories:", error);
  }
};

// Toggle category selection
const toggleCategory = (category) => {
  let updatedSelected = [...selectedCategories.value];

  if (updatedSelected.some((c) => c.id === category.id)) {
    updatedSelected = updatedSelected.filter((c) => c.id !== category.id);
  } else if (updatedSelected.length < 3) {
    updatedSelected.push(category);
  }

  selectedCategories.value = updatedSelected;
  emit("update:selected", updatedSelected);
};

// Sync `selectedCategories` when `props.selected` changes
watch(() => props.selected, (newVal) => {
  selectedCategories.value = categories.value.filter(category =>
    newVal.some(selected => selected?.id === category.id)
  );
}, { deep: true });

onMounted(() => {
  fetchCategories();
});
</script>

<style scoped>
button {
  transition: all 0.3s;
}
</style>