<script setup>
import { ref, onMounted, watchEffect } from "vue";
import { router } from '@inertiajs/vue3';
import StarRating from "@/Components/StarRating.vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SocialMediaSelector from "@/Components/SocialMediaIcons.vue";
import { Head } from '@inertiajs/vue3';

// Define props to accept review data from Laravel
const props = defineProps({
  reviews: Object,
  socialMediaplatform: Object, // Ensure this prop is passed from the backend
  reviewId: Number, // Add this prop to accept the record ID
});

// Reactive form fields
const qualityRating = ref(0);
const valueRating = ref(0);
const supportRating = ref(0);
const generalReview = ref("");
const selectedSocialMedia = ref({});
const isSubmitting = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

// Existing social media data to pre-fill the SocialMediaSelector
const existingSocialMediaData = ref(props.socialMediaplatform || {});

// Prefill form fields when component is mounted
onMounted(() => {
  if (props.reviews) { // Ensure reviews exist
    selectedSocialMedia.value = props.socialMediaplatform || {};
    qualityRating.value = props.reviews.performance || 0;
    valueRating.value = props.reviews.lead || 0;
    supportRating.value = props.reviews.overall_review || 0;
    generalReview.value = props.reviews.review || "";
  }
});

// Watch for changes in props and update the form fields
watchEffect(() => {
  if (props.reviews) {
    selectedSocialMedia.value = props.socialMediaplatform || {};
    qualityRating.value = props.reviews.performance || 0;
    valueRating.value = props.reviews.lead || 0;
    supportRating.value = props.reviews.overall_review || 0;
    generalReview.value = props.reviews.review || "";
  }
});

// Submit review function
const submitReview = () => {
  if (Object.keys(selectedSocialMedia.value).length === 0) {
    errorMessage.value = "Please select at least one social media platform.";
    return;
  }

  isSubmitting.value = true;
  errorMessage.value = "";

  // Include the review ID in the payload
  router.post(route('customer-review-update'), {
    id: props.reviewId, // Pass the record ID
    social_media: selectedSocialMedia.value,
    quality_rating: qualityRating.value,
    value_rating: valueRating.value,
    support_rating: supportRating.value,
    general_review: generalReview.value,
  }, {
    onSuccess: () => {
      successMessage.value = "Review submitted successfully!";
      isSubmitting.value = false;
    },
    onError: () => {
      errorMessage.value = "Failed to submit review. Please check your inputs.";
      isSubmitting.value = false;
    }
  });
};
</script>


<template>
  <Head title="Submit Review" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Submit Your Review</h2>
    </template>

    <div class="py-12 flex justify-center">
      <div class="w-full max-w-3xl px-6">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-8">
          
          <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100 text-center mb-6">
            Submit Your Review
          </h3>

          <div v-if="errorMessage" class="mb-4 p-3 bg-red-500 text-white rounded-lg">
            {{ errorMessage }}
          </div>

          <div v-if="successMessage" class="mb-4 p-3 bg-green-500 text-white rounded-lg">
            {{ successMessage }}
          </div>

          <div class="mb-6">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-semibold mb-2">
              Select Social Media Platforms
            </label>
            <div class="p-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700">
              <SocialMediaSelector 
                v-model:selected="selectedSocialMedia" 
                :existingData="existingSocialMediaData" 
              />
            </div>
          </div>
          
          <div class="mb-6 space-y-5">
            <div class="flex items-center justify-between">
              <span class="text-gray-700 dark:text-gray-300 text-sm font-semibold">Quality</span>
              <StarRating v-model="qualityRating" />
            </div>
            <div class="flex items-center justify-between">
              <span class="text-gray-700 dark:text-gray-300 text-sm font-semibold">Value for Money</span>
              <StarRating v-model="valueRating" />
            </div>
            <div class="flex items-center justify-between">
              <span class="text-gray-700 dark:text-gray-300 text-sm font-semibold">Customer Support</span>
              <StarRating v-model="supportRating" />
            </div>
          </div>

          <div class="mb-6">
            <label for="review" class="block text-gray-700 dark:text-gray-300 text-sm font-semibold mb-2">
              General Review
            </label>
            <textarea 
              id="review" 
              v-model="generalReview" 
              rows="5" 
              class="w-full p-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring focus:ring-blue-500 focus:border-blue-500"
              placeholder="Write your detailed review here..."
            ></textarea>
          </div>

          <div class="mt-6 flex justify-center">
            <button 
              @click="submitReview" 
              :disabled="isSubmitting"
              class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold py-3 px-8 rounded-lg shadow-md disabled:opacity-50">
              {{ isSubmitting ? "Submitting..." : "Submit Review" }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>