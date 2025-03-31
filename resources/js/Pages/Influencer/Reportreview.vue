<template>
    <!-- [Your existing template content remains the same] -->
    
    <!-- Add this inside each review card template -->
    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
        <button 
            @click="openReportModal(review.id)"
            class="text-xs text-red-500 dark:text-red-400 hover:underline flex items-center gap-1"
            aria-label="Report this review"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Report this review
        </button>
    </div>

    <!-- Report Modal -->
    <div v-if="showReportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="closeReportModal">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Report Review</h3>
                <button @click="closeReportModal" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300" aria-label="Close report modal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form @submit.prevent="submitReport">
                <div class="mb-4">
                    <label for="report-reason" class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2">
                        Reason for reporting
                    </label>
                    <select 
                        id="report-reason"
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
                    <label for="report-description" class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2">
                        Please specify
                    </label>
                    <textarea 
                        id="report-description"
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
                        class="px-4 py-2 bg-red-600 rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
                        :disabled="isSubmitting || !reportData.reason"
                    >
                        <span v-if="!isSubmitting">Submit Report</span>
                        <span v-else class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Notification -->
    <div v-if="showSuccessNotification" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg flex items-center gap-2 z-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span>Report submitted successfully!</span>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

// Report functionality
const showReportModal = ref(false);
const showSuccessNotification = ref(false);
const currentReviewId = ref(null);
const isSubmitting = ref(false);
const reportData = ref({
    reason: '',
    description: ''
});

const openReportModal = (reviewId) => {
    currentReviewId.value = reviewId;
    showReportModal.value = true;
};

const closeReportModal = () => {
    showReportModal.value = false;
    resetReportForm();
};

const resetReportForm = () => {
    reportData.value = {
        reason: '',
        description: ''
    };
    isSubmitting.value = false;
};

const submitReport = async () => {
    if (!reportData.value.reason || (reportData.value.reason === 'other' && !reportData.value.description)) {
        return;
    }

    isSubmitting.value = true;
    
    try {
        await router.post('/reports', {
            review_id: currentReviewId.value,
            reason: reportData.value.reason,
            description: reportData.value.reason === 'other' ? reportData.value.description : null
        });
        
        showSuccessNotification.value = true;
        closeReportModal();
        
        // Hide success notification after 3 seconds
        setTimeout(() => {
            showSuccessNotification.value = false;
        }, 3000);
    } catch (error) {
        alert('There was an error submitting your report. Please try again.');
    } finally {
        isSubmitting.value = false;
    }
};
</script>