<template>
    <div class="p-4 md:p-8 max-w-7xl mx-auto">
        <div v-if="reviews.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div 
                v-for="review in reviews" 
                :key="review.id"
                @click="!readonly && goToEditPage(review.id)" 
                class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-lg p-5 transition-all border border-gray-100 dark:border-gray-700"
                :class="readonly ? 'cursor-default' : 'cursor-pointer hover:-translate-y-1 hover:border-blue-100 dark:hover:border-blue-900/50'"
            >
                <!-- Header with platforms and date -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-2">
                        <font-awesome-icon 
                            v-for="platform in review.social_media" 
                            :key="platform" 
                            :icon="icons[platform]" 
                            class="w-4 h-4 p-1.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 transition group-hover:bg-blue-100 group-hover:text-blue-500" 
                        />
                    </div>
                    
                    <span v-if="review.created_at" class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                        {{ formatDate(review.created_at) }}
                    </span>
                </div>

                <!-- Overall rating stars -->
                <div class="flex items-center mb-3">
                    <div class="flex">
                        <template v-for="i in 5" :key="i">
                            <svg 
                                class="w-5 h-5" 
                                :class="i <= Math.round(review.overall_review) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'"
                                fill="currentColor" 
                                viewBox="0 0 20 20"
                            >
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </template>
                    </div>
                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ calculateOverallRating(review).toFixed(1) }}
                    </span>
                </div>

                <!-- Review Content -->
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3 line-clamp-2">
                    {{ review.title || "Great Experience" }}
                </h3>
                
                <!-- Rating bars -->
                <div class="space-y-3 mb-4">
                    <div v-for="(label, key) in ratingLabels" :key="key" class="flex items-center text-sm gap-2">
                        <span class="font-medium text-gray-600 dark:text-gray-300 w-20 flex-shrink-0 truncate">
                            {{ label }}:
                        </span>
                        <div class="flex-1 min-w-0 flex items-center gap-2">
                            <div class="w-full bg-gray-100 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                                <div 
                                    class="h-full rounded-full" 
                                    :class="getRatingColor(review[key])"
                                    :style="{ width: (review[key] / 5) * 100 + '%' }"
                                ></div>
                            </div>
                            <span class="font-medium w-8 flex-shrink-0 text-right" :class="getRatingTextColor(review[key])">
                                {{ review[key].toFixed(1) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Truncated Review -->
                <div class="relative">
                    <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed line-clamp-3 mb-2">
                        "{{ review.review }}"
                    </p>
                    <div class="absolute bottom-0 left-0 right-0 h-6 bg-gradient-to-t from-white dark:from-gray-800 to-transparent opacity-80"></div>
                </div>

                <!-- View/edit button (hidden if readonly) -->
                <div v-if="!readonly" class="mt-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    <span class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                        View/Edit Review
                    </span>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-12">
            <div class="mx-auto w-24 h-24 text-gray-300 dark:text-gray-600 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-600 dark:text-gray-300 mb-1">No reviews yet</h3>
            <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                You haven't received any reviews yet. Check back later or share your services to get feedback.
            </p>
        </div>
    </div>
</template>

<script setup>
import { defineProps } from "vue";
import { router } from "@inertiajs/vue3";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faTiktok, faFacebookF, faInstagram, faXTwitter } from "@fortawesome/free-brands-svg-icons";

const icons = {
    tiktok: faTiktok,
    facebook: faFacebookF,
    instagram: faInstagram,
    x: faXTwitter
};

// Accept the `readonly` prop
defineProps({
    reviews: {
        type: Array,
        required: true,
    },
    readonly: {
        type: Boolean,
        default: false
    }
});

// Mapping rating fields to labels
const ratingLabels = {
    performance: "Quality",
    lead: "Value",
    overall_review: "Support"
};

// Function to get color based on rating value
const getRatingColor = (rating) => {
    if (rating >= 4) return 'bg-green-500';
    if (rating >= 3) return 'bg-blue-500';
    if (rating >= 2) return 'bg-yellow-500';
    return 'bg-red-500';
};

// Function to get text color based on rating value
const getRatingTextColor = (rating) => {
    if (rating >= 4) return 'text-green-600 dark:text-green-400';
    if (rating >= 3) return 'text-blue-600 dark:text-blue-400';
    if (rating >= 2) return 'text-yellow-600 dark:text-yellow-400';
    return 'text-red-600 dark:text-red-400';
};

// Function to format date
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
};

// Function to navigate to the edit page
const goToEditPage = (reviewId) => {
    router.get(`/customer/reviews/${reviewId}/edit`);
};

const calculateOverallRating = (review) => {
    const total = (review.performance + review.lead + review.overall_review) / 3;
    return total || 0; // Avoid NaN issues
};

</script>

<style scoped>
/* Smooth transitions */
.group {
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Better line clamping for text */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Ensure cards have consistent width */
.grid {
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
}

@media (min-width: 640px) {
    .grid {
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    }
}

@media (min-width: 1024px) {
    .grid {
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    }
}
</style>