<script setup>
import { ref, computed, watchEffect } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SocialMediaSelector from '@/Components/SocialMediaIcons.vue';
import CategoryList from '@/Components/CategoryList.vue';
import { Head } from '@inertiajs/vue3';

// Get Inertia page props
const page = usePage();
const formErrors = computed(() => page.props.errors || {});
const successMessage = computed(() => page.props.flash?.success || '');

// Get authenticated user data
const user = computed(() => page.props.user);
const existingSocialMedia = computed(() => page.props.social_media || {});
const existingCategory = computed(() => page.props.category || []);

// Reactive state
const selectedSocialMedia = ref({ ...existingSocialMedia.value });
const selectedCategories = ref(Array.isArray(existingCategory.value) ? [...existingCategory.value] : Object.values(existingCategory.value));

// Initialize form
const form = useForm({
    social_media: selectedSocialMedia.value,
    categories: selectedCategories.value,
});

// Ensure form updates when `selectedSocialMedia` or `selectedCategories` change
watchEffect(() => {
    form.social_media = selectedSocialMedia.value;
    form.categories = selectedCategories.value;
});

// Handle form submission
const saveInfluencer = () => {
    if (selectedCategories.value.length > 3) {
        formErrors.value = { categories: 'You can select a maximum of 3 categories.' };
        return;
    }

    

    form.social_media = selectedSocialMedia.value;
    form.categories = selectedCategories.value;

    form.post(route('influencers.store'), {
        onSuccess: () => {
            console.log("Form submitted successfully!");
        },
        onError: (errors) => {
            console.error("Form submission failed:", errors);
        },
    });
};
</script>

<template>
    <Head title="My Socialmedia Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                My Socialmedia Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-900 shadow-lg rounded-2xl overflow-hidden">
                
                    <!-- Success Message -->
                    <div v-if="successMessage" class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                        {{ successMessage }}
                    </div>

                    <!-- Error Messages -->
                    <div v-if="Object.keys(formErrors).length" class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                        <ul>
                            <li v-for="(error, key) in formErrors" :key="key">{{ error }}</li>
                        </ul>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6 space-y-6">
                        <!-- Social Media Selector -->
                        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow">
                            <h4 class="text-md font-medium text-gray-800 dark:text-gray-300 mb-2">
                                Select Social Media
                            </h4>
                            <SocialMediaSelector 
                              v-model:selected="selectedSocialMedia" 
                              :existingData="existingSocialMedia" 
                              :showLinkField="false" 
                            />
                        </div>

                        <!-- Category List -->
                        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow">
                            <h4 class="text-md font-medium text-gray-800 dark:text-gray-300 mb-2">
                                Select Categories (Max 3)
                            </h4>
                            <CategoryList v-model:selected="selectedCategories" :existingData="existingCategory" />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button 
                                @click="saveInfluencer" 
                                class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold py-3 px-8 rounded-lg shadow-md disabled:opacity-50 hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Edit Detail
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>