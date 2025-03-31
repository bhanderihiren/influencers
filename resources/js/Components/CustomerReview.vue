<template>
    <div class="p-4 md:p-8 max-w-7xl mx-auto relative">
        <!-- Loading State -->
        <div v-if="!reviews" class="text-center py-12">
            <p class="text-gray-500 dark:text-gray-400">Loading reviews...</p>
        </div>

        <!-- Reloading Overlay -->
        <div v-if="isReloading" class="absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-80 flex items-center justify-center z-10 rounded-xl">
            <div class="flex items-center gap-3 p-4 bg-white dark:bg-gray-700 rounded-lg shadow-md">
                <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-700 dark:text-gray-300">Updating reviews...</span>
            </div>
        </div>

        <!-- Notification Toast -->
        <div v-if="showNotification" class="fixed top-4 right-4 z-50">
            <div :class="['px-4 py-2 rounded shadow-lg text-white flex items-center gap-2', 
                         notificationType === 'success' ? 'bg-green-500' : 'bg-red-500']">
                <span>{{ notificationMessage }}</span>
                <button @click="showNotification = false" class="text-white hover:text-white/80">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Report Status Modal -->
        <div v-if="showReportStatusModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Report Details</h3>
                    <button @click="showReportStatusModal = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div v-if="currentReportDetails" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                        <p class="mt-1 text-sm" 
                           :class="{
                             'text-yellow-600 dark:text-yellow-400': currentReportDetails.status === 'pending',
                             'text-green-600 dark:text-green-400': currentReportDetails.status === 'approved',
                             'text-red-600 dark:text-red-400': currentReportDetails.status === 'rejected'
                           }">
                            {{ currentReportDetails.status.toUpperCase() }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Reason</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ currentReportDetails.reason }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Submitted On</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ currentReportDetails.created_at }}</p>
                    </div>
                    
                    <div v-if="currentReportDetails.status !== 'pending'">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Updated On</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ currentReportDetails.updated_at }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content when loaded -->
        <template v-else>
            <!-- Controls Section -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <!-- Filter by Rating -->
                <div class="flex flex-wrap gap-2">
                    <button 
                        v-for="filter in ratingFilters" 
                        :key="filter.value"
                        @click="activeRatingFilter = filter.value"
                        class="px-3 py-1.5 rounded-full text-xs sm:text-sm font-medium transition-colors"
                        :class="{
                            'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300': activeRatingFilter === filter.value,
                            'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600': activeRatingFilter !== filter.value
                        }"
                    >
                        {{ filter.label }}
                    </button>
                </div>

                <!-- Sort Dropdown -->
                <div class="flex items-center gap-3">
                    <label for="sort" class="text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">Sort by:</label>
                    <select 
                        id="sort"
                        v-model="sortOption"
                        class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600"
                    >
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="highest">Highest Rated</option>
                        <option value="lowest">Lowest Rated</option>
                    </select>
                </div>
            </div>

            <!-- Reviews Grid -->
            <div v-if="filteredReviews.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div 
                    v-for="review in paginatedReviews" 
                    :key="review.id"
                    class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-lg p-5 transition-all border border-gray-100 dark:border-gray-700"
                    :class="{
                        'cursor-default': readonly,
                        'cursor-pointer hover:-translate-y-1 hover:border-blue-100 dark:hover:border-blue-900/50': !readonly,
                        'border-red-200 dark:border-red-900/50': isReporting(review.id)
                    }"
                >
                    <!-- Header with platforms, date, and report status -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <a 
                                v-for="platform in review.social_media || []" 
                                :key="platform.platform"
                                :href="String(platform.link) || '#'"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="w-6 h-6 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 transition group-hover:bg-blue-100 group-hover:text-blue-500"
                            >
                                <font-awesome-icon 
                                    :icon="icons[platform.platform]" 
                                    class="w-4 h-4 p-1.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 transition group-hover:bg-blue-100 group-hover:text-blue-500" 
                                />
                            </a>
                        </div> 
                        <div class="flex items-center">
                            <span v-if="review.created_at" class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ formatDate(review.created_at) }}
                            </span>
                        </div>
                    </div>

                    <!-- Overall rating stars -->
                    <div class="flex items-center mb-3">
                        <div class="flex">
                            <template v-for="i in 5" :key="i">
                                <svg 
                                    class="w-5 h-5" 
                                    :class="i <= Math.round(calculateOverallRating(review)) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'"
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
                                        :style="{ width: ((review[key] || 0) / 5 * 100) + '%' }"
                                    ></div>
                                </div>
                                <span class="font-medium w-8 flex-shrink-0 text-right" :class="getRatingTextColor(review[key])">
                                    {{ (review[key] || 0).toFixed(1) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Truncated Review -->
                    <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed line-clamp-3 mb-2">
                        {{ capitalizeFirstLetter(review.review || '') }}
                    </p>

                    <!-- Report button (only show if not already reported or if allowed to report again) -->
                    <div v-if="(!readonly || reportreview) && !review.report_details" class="mt-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <button @click="openReportModal(review.id)" class="text-xs text-red-500 dark:text-red-400 hover:underline flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Report Review
                        </button>
                    </div>
                    <span v-if="review.report_details" @click="showReportDetails(review)" class="text-xs px-2 py-1 rounded-full ml-2 cursor-pointer" :class="{ 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200': review.report_details.status === 'pending', 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200': review.report_details.status === 'approved','bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200': review.report_details.status === 'rejected'}">
                        Report {{ review.report_details.status }}
                    </span>
                </div>
            </div>

            <!-- Pagination Controls -->
            <div v-if="filteredReviews.length > itemsPerPage" class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredReviews.length) }} of {{ filteredReviews.length }} reviews
                </div>
                <div class="flex gap-1">
                    <button 
                        @click="currentPage = Math.max(1, currentPage - 1)"
                        :disabled="currentPage === 1"
                        class="px-3 py-1 rounded-md border border-gray-300 dark:border-gray-600 text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Previous
                    </button>
                    <button 
                        v-for="page in visiblePages" 
                        :key="page"
                        @click="currentPage = page"
                        class="w-10 h-10 rounded-md text-sm font-medium"
                        :class="page === currentPage 
                            ? 'bg-blue-600 text-white border border-blue-600' 
                            : 'border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                    >
                        {{ page }}
                    </button>
                    <button 
                        @click="currentPage = Math.min(totalPages, currentPage + 1)"
                        :disabled="currentPage === totalPages"
                        class="px-3 py-1 rounded-md border border-gray-300 dark:border-gray-600 text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Next
                    </button>
                </div>
            </div>

            <!-- No Reviews Message -->
            <div v-else-if="filteredReviews.length === 0" class="text-center py-12">
                <h3 class="text-lg font-medium text-gray-600 dark:text-gray-300 mb-1">No reviews match your filters</h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                    Try adjusting your filters to see more results.
                </p>
            </div>
        </template>

        <!-- Report Modal -->
        <div v-if="showReportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Report Review</h3>
                    <button @click="closeReportModal" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <form @submit.prevent="submitReport">
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2">
                            Reason for reporting
                        </label>
                        <select 
                            v-model="reportData.reason"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700"
                            required
                        >
                            <option value="" disabled>Select a reason</option>
                            <option value="fake">Fake or spam review</option>
                            <option value="wrong">Wrong information</option>
                            <option value="offensive">Offensive content</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-4" v-if="reportData.reason === 'other'">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2">
                            Please specify
                        </label>
                        <textarea 
                            v-model="reportData.description" 
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700"
                            placeholder="Please describe the issue..."
                            required
                        ></textarea>
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button 
                            type="button" 
                            @click="closeReportModal"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 bg-red-600 rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            :disabled="!reportData.reason || isSubmitting"
                        >
                            <span v-if="!isSubmitting">Submit Report</span>
                            <span v-else class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Submitting...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, reactive, watch, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { 
    faTiktok, 
    faFacebookF, 
    faInstagram, 
    faXTwitter 
} from "@fortawesome/free-brands-svg-icons";
import axios from 'axios';

const icons = {
    tiktok: faTiktok,
    facebook: faFacebookF,
    instagram: faInstagram,
    x: faXTwitter
};

const props = defineProps({
    reviews: {
        type: Array,
        default: () => []
    },
    readonly: {
        type: Boolean,
        default: false
    },
    reportreview: {
        type: Boolean,
        default: false
    },
    reloadOnSubmit: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['reload-reviews']);

// Filtering
const ratingFilters = [
    { value: 'all', label: 'All Ratings' },
    { value: '5', label: '5 Stars' },
    { value: '4', label: '4+ Stars' },
    { value: '3', label: '3+ Stars' },
    { value: '2', label: '2+ Stars' }
];
const activeRatingFilter = ref('all');

// Sorting
const sortOption = ref('newest');

// Pagination
const itemsPerPage = 12;
const currentPage = ref(1);

const ratingLabels = {
    performance: "Quality",
    lead: "Value",
    overall_review: "Support"
};

// Notification state
const showNotification = ref(false);
const notificationMessage = ref('');
const notificationType = ref('success'); // 'success' or 'error'

// Report functionality
const showReportModal = ref(false);
const showReportStatusModal = ref(false);
const currentReviewId = ref(null);
const currentReportDetails = ref(null);
const isSubmitting = ref(false);
const isReloading = ref(false);
const reportData = ref({
    reason: '',
    description: ''
});

// Computed properties
const filteredReviews = computed(() => {
    if (!props.reviews || !Array.isArray(props.reviews)) return [];
    
    let results = [...props.reviews];
    
    // Apply rating filter
    if (activeRatingFilter.value !== 'all') {
        const minRating = parseInt(activeRatingFilter.value);
        results = results.filter(review => {
            const overall = calculateOverallRating(review);
            return overall >= minRating;
        });
    }
    
    // Apply sorting
    switch (sortOption.value) {
        case 'newest':
            return results.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0));
        case 'oldest':
            return results.sort((a, b) => new Date(a.created_at || 0) - new Date(b.created_at || 0));
        case 'highest':
            return results.sort((a, b) => calculateOverallRating(b) - calculateOverallRating(a));
        case 'lowest':
            return results.sort((a, b) => calculateOverallRating(a) - calculateOverallRating(b));
        default:
            return results;
    }
});

const totalPages = computed(() => Math.ceil(filteredReviews.value.length / itemsPerPage));
const paginatedReviews = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredReviews.value.slice(start, end);
});

const visiblePages = computed(() => {
    const pages = [];
    const maxVisible = 5;
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

// Methods
const getRatingColor = (rating) => {
    rating = rating || 0;
    if (rating >= 4) return 'bg-green-500';
    if (rating >= 3) return 'bg-blue-500';
    if (rating >= 2) return 'bg-yellow-500';
    return 'bg-red-500';
};

const getRatingTextColor = (rating) => {
    rating = rating || 0;
    if (rating >= 4) return 'text-green-600 dark:text-green-400';
    if (rating >= 3) return 'text-blue-600 dark:text-blue-400';
    if (rating >= 2) return 'text-yellow-600 dark:text-yellow-400';
    return 'text-red-600 dark:text-red-400';
};

const formatDate = (dateString) => {
    return dateString ? new Date(dateString).toLocaleDateString("en-US", { year: "numeric", month: "short", day: "numeric" }) : "Unknown";
};

const calculateOverallRating = (review) => {
    return ((review?.performance || 0) + (review?.lead || 0) + (review?.overall_review || 0)) / 3 || 0;
};

const capitalizeFirstLetter = (text) => {
    return text ? text.charAt(0).toUpperCase() + text.slice(1) : "";
};

const isReporting = (reviewId) => {
    return false;
};

// Report modal methods
const openReportModal = (reviewId) => {
    currentReviewId.value = reviewId;
    showReportModal.value = true;
};

const closeReportModal = () => {
    showReportModal.value = false;
    resetReportForm();
};

const showReportDetails = (review) => {
    if (review.report_details) {
        currentReportDetails.value = review.report_details;
        showReportStatusModal.value = true;
    }
};

const resetReportForm = () => {
    reportData.value = {
        reason: '',
        description: ''
    };
    isSubmitting.value = false;
};

const showNotificationMessage = (message, type = 'success') => {
    notificationMessage.value = message;
    notificationType.value = type;
    showNotification.value = true;
    
    setTimeout(() => {
        showNotification.value = false;
    }, 3000);
};

const submitReport = async () => {
    if (!reportData.value.reason) return;
    
    isSubmitting.value = true;
    
    try {
        const response = await axios.post(route('influencer.reports.store'), {
            review_id: currentReviewId.value,
            reason: reportData.value.reason,
            description: reportData.value.reason === 'other' 
                ? reportData.value.description 
                : null
        });

        showNotificationMessage('Report submitted successfully!');
        closeReportModal();
        
        // Use Inertia's reload method
        if (props.reloadOnSubmit) {
            router.reload({ only: ['reviews'] });
        }
        
    } catch (error) {
        console.error('Error submitting report:', error);
        showNotificationMessage(
            error.response?.data?.message || 'Failed to submit report', 
            'error'
        );
    } finally {
        isSubmitting.value = false;
    }
};

// Reset current page when filters change
watch([activeRatingFilter, sortOption], () => {
    currentPage.value = 1;
});
</script>