<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CustomerReview from '@/Components/CustomerReview.vue';
import { defineProps } from "vue";
import { Head } from '@inertiajs/vue3';

defineProps({
    reviews: Array
});
</script>

<template>
    <Head title="My Reviews" />
    <AuthenticatedLayout>
        <div class="max-w-3xl mx-auto py-12">
            <CustomerReview :reviews="reviews" />
        </div>
    </AuthenticatedLayout>
</template>