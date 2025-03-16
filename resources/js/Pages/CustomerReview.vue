<script setup>
import CustomerReview from '@/Components/CustomerReview.vue';
import { defineProps } from "vue";
import { Head } from '@inertiajs/vue3';

defineProps({
    reviews: Array
});
</script>

<template>
    <Head title="Customer Reviews hello" />
    <div class="max-w-3xl mx-auto py-12">
        <h2 class="text-2xl font-semibold text-center text-gray-800 dark:text-gray-200">Customer Reviews</h2>
        <CustomerReview :reviews="reviews" />
    </div>
</template>
