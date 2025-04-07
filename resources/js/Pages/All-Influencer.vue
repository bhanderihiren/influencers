<template>
  <Head title="Influencers" />
  <AuthMenu :can-login="canLogin" :can-register="canRegister" />
  

   <div class="relative min-h-screen bg-gray-100 dark:bg-gray-900 selection:bg-red-500 selection:text-white">
    <main class="container mx-auto px-4 py-12">
      <div class="p-4 md:p-8 max-w-7xl mx-auto relative">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Influencer Directory</h1>
            <p class="text-gray-500 mt-2">
              Browse our network of {{ displayInfluencerCount }} influencers
            </p>
          </div>
          
          <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <!-- Search Input -->
            <div class="relative flex-1 md:w-64">
              <input 
                type="text" 
                v-model="searchQuery"
                @input="resetPagination"
                placeholder="Search by name..." 
                class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
              <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            
            <!-- Platform Filter -->
            <select 
              v-model="selectedPlatform"
              @change="resetPagination"
              class="border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">All Platforms</option>
              <option v-for="platform in platforms" :value="platform" class="capitalize">
                {{ platform }}
              </option>
            </select>
            
            <!-- Sort Options -->
            <select 
              v-model="sortBy"
              class="border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="followers">Sort by: Followers</option>
              <option value="name">Sort by: Name</option>
              <option value="platforms">Sort by: Platform Count</option>
            </select>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="inline-flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Loading influencers...
          </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredInfluencers.length === 0" class="text-center py-12">
          <p class="text-gray-500 text-lg">No influencers found matching your criteria</p>
          <button 
            @click="resetFilters"
            class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
          >
            Reset filters
          </button>
        </div>

        <!-- Main Content -->
        <div v-else>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
              v-for="influencer in paginatedInfluencers" 
              :key="influencer.id" 
              class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 border border-gray-100"
            >
              <div class="p-6">
                <!-- Influencer Header -->
                <div class="flex items-start gap-4 mb-4">
                  <img :src="influencer.avatar" :alt="`${influencer.name}'s avatar`" class="w-14 h-14 rounded-full object-cover border-2 border-white shadow flex-shrink-0" @error="handleImageError(influencer)" />
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                      <h2 class="text-lg font-semibold text-gray-900 truncate">{{ influencer.name }}</h2>
                      <span v-if="(influencer.is_verified && influencer.is_verified !=0 )" class="text-blue-500" title="Verified">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                      </span>
                    </div>
                    <p v-if="influencer.bio" class="text-sm text-gray-500 mt-1 line-clamp-2">{{ influencer.bio }}</p>
                  </div>
                </div>

                <!-- Social Platforms -->
                <div class="mt-4">
                  <h3 class="text-sm font-semibold text-gray-600 mb-2">Social Platforms</h3>
                  <div class="flex flex-wrap gap-2">
                    <a 
                      v-for="platform in influencer.platforms" 
                      :key="platform.platform"
                      :href="platform.link || '#'" 
                      target="_blank" 
                      rel="noopener noreferrer"
                      class="flex items-center px-3 py-1 rounded-full bg-gray-100 text-gray-600 hover:bg-blue-100 hover:text-blue-500 transition text-sm"
                      :title="`${platform.username} (${platform.followers || 'N/A'} followers)`"
                    >
                      <font-awesome-icon 
                        :icon="platform.icon" 
                        class="w-4 h-4 mr-1" 
                      />
                      {{ platform.platform }}
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="mt-8 flex justify-center">
            <nav class="inline-flex rounded-md shadow">
              <button 
                @click="currentPage--"
                :disabled="currentPage === 1"
                class="px-3 py-2 rounded-l-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Previous
              </button>
              <button 
                v-for="page in visiblePages"
                :key="page"
                @click="currentPage = page"
                :class="{
                  'px-4 py-2 border-t border-b border-gray-300 bg-white text-gray-500 hover:bg-gray-50': page !== currentPage,
                  'px-4 py-2 border-t border-b border-blue-500 bg-blue-50 text-blue-600': page === currentPage
                }"
              >
                {{ page }}
              </button>
              <button 
                @click="currentPage++"
                :disabled="currentPage === totalPages"
                class="px-3 py-2 rounded-r-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Next
              </button>
            </nav>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { library } from '@fortawesome/fontawesome-svg-core';
import AuthMenu from '@/Components/AuthMenu.vue';

import { 
  faTiktok, 
  faFacebookF, 
  faInstagram, 
  faTwitter,
  faYoutube,
  faLinkedinIn,
} from '@fortawesome/free-brands-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

// Add icons to library
library.add(
  faTiktok,
  faFacebookF,
  faInstagram,
  faTwitter,
  faYoutube,
  faLinkedinIn,
);
const navigation = [
  { name: 'Influencers', href: 'all-influencer' },
  { name: 'Brands', href: 'brands.index' },
  { name: 'Pricing', href: 'pricing' },
  { name: 'About', href: 'about' },
];

// Mobile menu state
const mobileMenuOpen = ref(false);

const props = defineProps({
  influencers: {
    type: Array,
    required: true,
    default: () => []
  },
  platforms: {
    type: Array,
    required: true,
    default: () => []
  }
});

// Reactive state
const searchQuery = ref('');
const selectedPlatform = ref('');
const sortBy = ref('followers');
const loading = ref(false);
const currentPage = ref(1);
const itemsPerPage = 9;

// Computed properties
const displayInfluencerCount = computed(() => {
  return filteredInfluencers.value.length === props.influencers.length 
    ? props.influencers.length 
    : `${filteredInfluencers.value.length} of ${props.influencers.length}`;
});

const filteredInfluencers = computed(() => {
  let results = [...props.influencers];
  
  // Filter by search
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    results = results.filter(influencer => 
      (influencer.name && influencer.name.toLowerCase().includes(query)) ||
      (influencer.bio && influencer.bio.toLowerCase().includes(query))
    );
  }
  
  // Filter by platform
  if (selectedPlatform.value) {
    results = results.filter(influencer => 
      influencer.platforms?.some(p => p.platform === selectedPlatform.value)
    );
  }
  
  // Sort results
  switch (sortBy.value) {
    case 'name':
      results.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
      break;
    case 'platforms':
      results.sort((a, b) => (b.platforms?.length || 0) - (a.platforms?.length || 0));
      break;
    case 'followers':
    default:
      results.sort((a, b) => (b.total_followers || 0) - (a.total_followers || 0));
  }
  
  return results;
});

const totalPages = computed(() => {
  return Math.ceil(filteredInfluencers.value.length / itemsPerPage);
});

const visiblePages = computed(() => {
  const maxVisible = 5;
  const pages = [];
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
  let end = Math.min(totalPages.value, start + maxVisible - 1);
  
  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1);
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  
  return pages;
});

const paginatedInfluencers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredInfluencers.value.slice(start, end);
});

// Methods
const resetPagination = () => {
  currentPage.value = 1;
};

const resetFilters = () => {
  searchQuery.value = '';
  selectedPlatform.value = '';
  sortBy.value = 'followers';
  resetPagination();
};

const handleImageError = (influencer) => {
  influencer.avatar = '/default-avatar.png';
};

// Watchers
watch([searchQuery, selectedPlatform], () => {
  loading.value = true;
  setTimeout(() => {
    loading.value = false;
  }, 300);
});

watch(currentPage, (newVal, oldVal) => {
  if (newVal !== oldVal) {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
});
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

@media (max-width: 640px) {
  .filter-controls {
    flex-direction: column;
  }
}
</style>