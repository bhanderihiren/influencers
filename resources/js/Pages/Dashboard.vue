<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SocialMediaSelector from '@/Components/SocialMediaIcons.vue';
import StarRating from "@/Components/StarRating.vue";
import { ref } from "vue";
import { router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';

const qualityRating = ref(0);
const valueRating = ref(0);
const supportRating = ref(0);
const generalReview = ref("");
const selectedSocialMedia = ref({});
const isSubmitting = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

// Reset form function
const resetForm = () => {
    selectedSocialMedia.value = {};  // Reset social media selection
    qualityRating.value = 0;         // Reset quality rating
    valueRating.value = 0;           // Reset value rating
    supportRating.value = 0;         // Reset support rating
    generalReview.value = "";        // Reset general review
};

// Submit review function
const submitReview = () => {
    if (Object.keys(selectedSocialMedia.value).length === 0) {
        errorMessage.value = "Please select at least one social media platform.";
        return;
    }

    isSubmitting.value = true;
    errorMessage.value = "";

    router.post(route('customer-review'), {
        social_media: selectedSocialMedia.value,
        quality_rating: qualityRating.value,
        value_rating: valueRating.value,
        support_rating: supportRating.value,
        general_review: generalReview.value,
    }, {
        onSuccess: () => {
            successMessage.value = "Review submitted successfully!";
            resetForm();  // Reset form fields after success
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
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="py-12 flex justify-center">
            <p>Dashboard</p>
        </div>
    </AuthenticatedLayout>
</template>