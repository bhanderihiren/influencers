<template>
    <div class="p-8">
        <div v-if="reviews.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div v-for="review in reviews" :key="review.id"
                 @click="goToEditPage(review.id)" 
                 class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl p-6 transition-all border border-gray-200 dark:border-gray-700 cursor-pointer">
                
                <!-- Top Section: Social Media Icons -->
                <div class="flex items-center justify-between">
                    <div class="flex space-x-3">
                        <font-awesome-icon 
                            v-for="platform in review.social_media" 
                            :key="platform" 
                            :icon="icons[platform]" 
                            class="w-6 h-6 text-gray-500 dark:text-gray-300 group-hover:text-blue-500 transition" />
                    </div>
                </div>

                <!-- Ratings Section -->
                <div class="mt-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                        ⭐ Ratings
                    </h3>
                    <div class="mt-2 text-gray-700 dark:text-gray-300 text-sm space-y-1">
                        <p class="flex items-center">
                            <span class="font-semibold w-20">Quality:</span> 
                            <span class="ml-2 text-yellow-500">⭐ {{ review.performance }}/5</span>
                        </p>
                        <p class="flex items-center">
                            <span class="font-semibold w-20">Value:</span> 
                            <span class="ml-2 text-yellow-500">⭐ {{ review.lead }}/5</span>
                        </p>
                        <p class="flex items-center">
                            <span class="font-semibold w-20">Support:</span> 
                            <span class="ml-2 text-yellow-500">⭐ {{ review.overall_review }}/5</span>
                        </p>
                    </div>
                </div>

                <!-- Truncated Review -->
                <p class="mt-4 text-gray-600 dark:text-gray-400 text-sm italic leading-relaxed">
                    "{{ truncateText(review.review, 120) }}"
                </p>

                <!-- Floating Effect -->
                <div class="absolute inset-0 bg-gradient-to-br from-transparent to-blue-100 dark:to-gray-700 opacity-0 group-hover:opacity-10 rounded-2xl transition-all"></div>
            </div>
        </div>

        <p v-else class="text-center text-gray-600 dark:text-gray-300 text-lg mt-8">
            No reviews available.
        </p>
    </div>
</template>

<script setup>
import { defineProps } from "vue";
import { router } from '@inertiajs/vue3';
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faTiktok, faFacebookF, faInstagram, faXTwitter } from "@fortawesome/free-brands-svg-icons";

const icons = {
    tiktok: faTiktok,
    facebook: faFacebookF,
    instagram: faInstagram,
    x: faXTwitter
};

defineProps({
    reviews: {
        type: Array,
        required: true,
    }
});

// Function to navigate to the edit page
const goToEditPage = (reviewId) => {
    router.get(`/customer/reviews/${reviewId}/edit`);
};

// Function to truncate text
const truncateText = (text, length) => {
    return text.length > length ? text.substring(0, length) + "..." : text;
};
</script>

<style scoped>
/* Floating effect on hover */
.group:hover {
    transform: translateY(-4px);
    transition: all 0.3s ease-in-out;
}

/* Card border effect */
.group {
    border-left: 4px solid #3b82f6;
}
</style>