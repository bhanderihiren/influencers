<template>
  <div class="influencer-directory">
    <!-- Header Section -->
    <header class="directory-header">
      <div class="header-content">
        <h1>Influencer Directory</h1>
        <p class="influencer-count">
          Browse our network of {{ displayInfluencerCount }} influencers
        </p>
      </div>

      <div class="filter-controls">
        <SearchInput
          v-model="searchQuery"
          placeholder="Search by name..."
          @update:modelValue="resetPagination"
        />
        
        <PlatformSelect
          v-model="selectedPlatform"
          :platforms="platforms"
          @update:modelValue="resetPagination"
        />
        
        <SortSelect
          v-model="sortBy"
          :options="sortOptions"
        />
      </div>
    </header>

    <!-- Loading State -->
    <LoadingSpinner v-if="loading" />

    <!-- Empty State -->
    <EmptyState
      v-else-if="filteredInfluencers.length === 0"
      @reset-filters="resetFilters"
    />

    <!-- Main Content -->
    <main v-else>
      <InfluencerGrid 
        :influencers="paginatedInfluencers"
        @influencer-click="handleInfluencerClick"
      />
      
      <PaginationControls
        v-if="showPagination"
        :current-page="currentPage"
        :total-pages="totalPages"
        :visible-pages="visiblePages"
        @page-change="handlePageChange"
      />
    </main>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { debounce } from 'lodash-es';

const ITEMS_PER_PAGE = 9;

const props = defineProps({
  influencers: Array,
  platforms: Array
});

const emit = defineEmits(['influencer-selected']);

const searchQuery = ref('');
const selectedPlatform = ref('');
const sortBy = ref('followers');
const loading = ref(false);
const currentPage = ref(1);

const sortOptions = [
  { value: 'followers', label: 'Sort by: Followers' },
  { value: 'name', label: 'Sort by: Name' },
  { value: 'platforms', label: 'Sort by: Platform Count' }
];

const filteredInfluencers = computed(() => {
  let results = [...props.influencers];

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    results = results.filter(i =>
      i.name.toLowerCase().includes(query) ||
      (i.bio && i.bio.toLowerCase().includes(query))
    );
  }

  if (selectedPlatform.value) {
    results = results.filter(i =>
      i.platforms?.some(p => p.platform === selectedPlatform.value)
    );
  }

  return sortInfluencers(results, sortBy.value);
});

const displayInfluencerCount = computed(() => {
  const total = props.influencers.length;
  const filtered = filteredInfluencers.value.length;
  return filtered === total ? total : `${filtered} of ${total}`;
});

const { totalPages, visiblePages, paginatedInfluencers } = usePagination(
  filteredInfluencers,
  currentPage,
  ITEMS_PER_PAGE
);

const showPagination = computed(() => totalPages.value > 1);

const resetPagination = () => {
  currentPage.value = 1;
};

const resetFilters = () => {
  searchQuery.value = '';
  selectedPlatform.value = '';
  sortBy.value = 'followers';
  resetPagination();
};

const handlePageChange = (page) => {
  currentPage.value = page;
};

const handleInfluencerClick = (influencer) => {
  emit('influencer-selected', influencer);
};

const sortInfluencers = (list, key) => {
  const sorted = [...list];
  switch (key) {
    case 'name':
      return sorted.sort((a, b) => a.name.localeCompare(b.name));
    case 'platforms':
      return sorted.sort((a, b) => (b.platforms?.length || 0) - (a.platforms?.length || 0));
    default:
      return sorted.sort((a, b) => (b.total_followers || 0) - (a.total_followers || 0));
  }
};

function usePagination(data, currentPage, itemsPerPage) {
  const totalPages = computed(() => Math.ceil(data.value.length / itemsPerPage));

  const visiblePages = computed(() => {
    const maxVisible = 5;
    let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
    let end = Math.min(totalPages.value, start + maxVisible - 1);
    if (end - start + 1 < maxVisible) {
      start = Math.max(1, end - maxVisible + 1);
    }
    return Array.from({ length: end - start + 1 }, (_, i) => start + i);
  });

  const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    return data.value.slice(start, start + itemsPerPage);
  });

  return { totalPages, visiblePages, paginatedData };
}

watch([searchQuery, selectedPlatform], debounce(() => {
  loading.value = true;
  setTimeout(() => loading.value = false, 300);
}, 300));
</script>

<style scoped>
.influencer-directory {
  @apply max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8;
}
.directory-header {
  @apply flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4;
}
.header-content h1 {
  @apply text-3xl font-bold text-gray-900;
}
.influencer-count {
  @apply text-gray-500 mt-2;
}
.filter-controls {
  @apply flex flex-col sm:flex-row gap-3 w-full md:w-auto;
}
@media (max-width: 640px) {
  .filter-controls {
    @apply flex-col;
  }
}
</style>

<!-- Inline Components -->

<script>
export const SearchInput = {
  props: ['modelValue', 'placeholder'],
  emits: ['update:modelValue'],
  template: `
    <div class="relative flex-1 md:w-64">
      <input
        type="text"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :placeholder="placeholder"
        class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
      />
      <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
      </svg>
    </div>
  `
};

export const PlatformSelect = {
  props: ['modelValue', 'platforms'],
  emits: ['update:modelValue'],
  template: `
    <select
      :value="modelValue"
      @change="$emit('update:modelValue', $event.target.value)"
      class="border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
    >
      <option value="">All Platforms</option>
      <option
        v-for="platform in platforms"
        :key="platform"
        :value="platform"
        class="capitalize"
      >
        {{ platform }}
      </option>
    </select>
  `
};

export const SortSelect = {
  props: ['modelValue', 'options'],
  emits: ['update:modelValue'],
  template: `
    <select
      :value="modelValue"
      @change="$emit('update:modelValue', $event.target.value)"
      class="border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
    >
      <option
        v-for="option in options"
        :key="option.value"
        :value="option.value"
      >
        {{ option.label }}
      </option>
    </select>
  `
};

export const LoadingSpinner = {
  template: `
    <div class="text-center py-12">
      <div class="inline-flex items-center">
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        Loading influencers...
      </div>
    </div>
  `
};

export const EmptyState = {
  emits: ['reset-filters'],
  template: `
    <div class="text-center py-12">
      <p class="text-gray-500 text-lg">No influencers found matching your criteria</p>
      <button
        @click="$emit('reset-filters')"
        class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
      >
        Reset filters
      </button>
    </div>
  `
};

export const InfluencerGrid = {
  props: ['influencers'],
  emits: ['influencer-click'],
  components: {
    InfluencerCard: {
      props: ['influencer'],
      emits: ['click'],
      template: `
        <div class="bg-white p-4 rounded-lg shadow hover:shadow-md transition cursor-pointer" @click="$emit('click')">
          <h2 class="text-lg font-semibold text-gray-900">{{ influencer.name }}</h2>
          <p class="text-sm text-gray-500 truncate">{{ influencer.bio || 'No bio available.' }}</p>
          <div class="mt-2 flex flex-wrap gap-1">
            <span
              v-for="p in influencer.platforms || []"
              :key="p.platform"
              class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full"
            >
              {{ p.platform }}
            </span>
          </div>
          <div class="mt-2 text-sm text-gray-700">Followers: {{ influencer.total_followers ?? 0 }}</div>
        </div>
      `
    }
  },
  template: `
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <InfluencerCard
        v-for="influencer in influencers"
        :key="influencer.id"
        :influencer="influencer"
        @click="$emit('influencer-click', influencer)"
      />
    </div>
  `
};

export const PaginationControls = {
  props: ['currentPage', 'totalPages', 'visiblePages'],
  emits: ['page-change'],
  components: {
    PaginationButton: {
      props: ['active', 'disabled'],
      emits: ['click'],
      template: `
        <button
          class="px-4 py-2 border text-sm font-medium"
          :class="[
            active ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100',
            disabled ? 'cursor-not-allowed opacity-50' : ''
          ]"
          :disabled="disabled"
          @click="$emit('click')"
        >
          <slot />
        </button>
      `
    }
  },
  template: `
    <div class="mt-8 flex justify-center">
      <nav class="inline-flex rounded-md shadow">
        <PaginationButton
          @click="$emit('page-change', currentPage - 1)"
          :disabled="currentPage === 1"
        >
          Previous
        </PaginationButton>
        <PaginationButton
          v-for="page in visiblePages"
          :key="page"
          @click="$emit('page-change', page)"
          :active="page === currentPage"
        >
          {{ page }}
        </PaginationButton>
        <PaginationButton
          @click="$emit('page-change', currentPage + 1)"
          :disabled="currentPage === totalPages"
        >
          Next
        </PaginationButton>
      </nav>
    </div>
  `
};
</script>