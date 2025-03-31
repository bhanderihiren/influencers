<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CustomerReview from '@/Components/CustomerReview.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    reviews: {
        type: Array,
        default: () => []
    },
    loading: {
        type: Boolean,
        default: false
    }
});

const recommendationRate = computed(() => {
    if (!props.reviews?.length) return 0;
    const recommended = props.reviews.filter(review => review.is_recommended).length;
    return Math.round((recommended / props.reviews.length) * 100);
});

// Filtering functionality
const activeFilter = ref('all');
const sortOption = ref('most_recent');

const filteredReviews = computed(() => {
    let result = [...props.reviews];
    
    // Apply rating filter
    if (activeFilter.value !== 'all') {
        const rating = parseInt(activeFilter.value);
        result = result.filter(review => review.rating === rating);
    }
    
    // Apply sorting
    switch (sortOption.value) {
        case 'highest_rated':
            return result.sort((a, b) => b.rating - a.rating);
        case 'lowest_rated':
            return result.sort((a, b) => a.rating - b.rating);
        case 'most_recent':
        default:
            return result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    }
});
</script>

<template>
    <Head title="Customer Reviews" />
    <AuthenticatedLayout>
        <div class="container mx-auto px-4 py-8">
            <!-- Loading state -->
            <div v-if="loading" class="text-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500 mx-auto mb-4"></div>
                <p>Loading reviews...</p>
            </div>

            <!-- Content when loaded -->
            <div v-else class="max-w-6xl mx-auto">
                <!-- Page Header -->
                <div class="mb-8 text-center">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Your Reviews</h1>
                    <p class="text-gray-600">Feedback from your customers</p>
                </div>

                <!-- Stats Summary -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="text-center">
                        <p class="text-4xl font-bold text-indigo-600">{{ reviews?.length || 0 }}</p>
                        <p class="text-gray-500">Total Reviews Received</p>
                    </div>
                    <div class="text-center">
                        <p class="text-4xl font-bold text-indigo-600">{{ recommendationRate }}%</p>
                        <p class="text-gray-500">Positive Feedback</p>
                    </div>
                </div>

                <!-- Reviews List -->
                <CustomerReview 
                    :reviews="filteredReviews" 
                    class="mb-8"
                />

                <!-- Empty State -->
                <div 
                    v-if="!loading && (!reviews || reviews.length === 0)" 
                    class="bg-white rounded-xl shadow-sm p-12 text-center"
                >
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">No reviews yet</h3>
                    <p class="mt-1 text-gray-500">Your customers haven't left feedback yet</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>