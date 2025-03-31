<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CustomerReview from '@/Components/CustomerReview.vue';
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    reviews: {
        type: Array,
        default: () => []
    }
});

const calculateOverallRating = (review) => {
    return ((review.performance ?? 0) + (review.lead ?? 0) + (review.overall_review ?? 0)) / 3 || 0;
};

// Compute the Average Rating across all reviews
const averageRating = computed(() => {
    if (!props.reviews.length) return { value: "0.0", stars: 0 };

    let totalSum = 0;
    let totalCount = 0;

    props.reviews.forEach(review => {
        const rating = calculateOverallRating(review);
        if (rating > 0) {
            totalSum += rating;
            totalCount++;
        }
    });

    const avg = totalCount > 0 ? totalSum / totalCount : 0;
    return { value: avg.toFixed(1), stars: Math.round(avg) };
});


// Determine experience level with icons
const experienceLevel = computed(() => {
    const count = props.reviews?.length || 0;
    if (count >= 20) return { text: 'Expert Influencer', icon: '🏆', color: 'bg-purple-100 text-purple-800' };
    if (count >= 10) return { text: 'Experienced Influencer', icon: '🌟', color: 'bg-blue-100 text-blue-800' };
    if (count >= 5) return { text: 'Growing Influencer', icon: '🌱', color: 'bg-green-100 text-green-800' };
    return { text: 'New Influencer', icon: '🆕', color: 'bg-yellow-100 text-yellow-800' };
});

// Generate motivational message with icons
const motivationalMessage = computed(() => {
    const avg = parseFloat(averageRating.value.value);
    if (avg >= 4.5) return { text: 'You\'re crushing it! Your audience loves your content.', icon: '🚀', color: 'bg-gradient-to-r from-purple-500 to-pink-500' };
    if (avg >= 4.0) return { text: 'Great work! Keep creating amazing content.', icon: '👍', color: 'bg-gradient-to-r from-blue-500 to-teal-400' };
    if (avg >= 3.0) return { text: 'Good progress! There\'s room to grow even more.', icon: '📈', color: 'bg-gradient-to-r from-green-500 to-emerald-400' };
    return { text: 'Every influencer starts somewhere! Check our tips to improve.', icon: '💡', color: 'bg-gradient-to-r from-yellow-500 to-amber-400' };
});
</script>

<template>
    <Head title="My Reviews" />
    <AuthenticatedLayout>
        <main class="container mx-auto px-4 py-8">
            <!-- Hero Section -->
            <section class="mb-10">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-2xl p-8 text-white shadow-xl">
                    <h1 class="text-3xl md:text-4xl font-bold mb-2">Your Influencer Dashboard</h1>
                    <p class="text-lg opacity-90">Track your performance and grow your influence</p>
                </div>
            </section>

            <!-- Stats Cards -->
            <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <!-- Average Rating Card -->
                <div class="bg-white rounded-xl shadow-lg p-6 flex items-center hover:shadow-xl transition-shadow duration-300">
                    <div class="mr-4 p-3 bg-indigo-100 rounded-full">
                        <span class="text-indigo-600 text-2xl">⭐</span>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Average Rating</p>
                        <div class="flex items-center">
                            <h3 class="text-3xl font-bold mr-2">{{ averageRating.value }}</h3>
                            <div class="flex">
                                <span v-for="star in 5" :key="star" 
                                      :class="['text-xl', star <= averageRating.stars ? 'text-yellow-400' : 'text-gray-300']">
                                    ★
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Total Reviews Card -->
                <div class="bg-white rounded-xl shadow-lg p-6 flex items-center hover:shadow-xl transition-shadow duration-300">
                    <div class="mr-4 p-3 bg-green-100 rounded-full">
                        <span class="text-green-600 text-2xl">📝</span>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Reviews</p>
                        <h3 class="text-3xl font-bold">{{ reviews.length }}</h3>
                    </div>
                </div>

                <!-- Experience Level Card -->
                <div class="bg-white rounded-xl shadow-lg p-6 flex items-center hover:shadow-xl transition-shadow duration-300">
                    <div class="mr-4 p-3 rounded-full" :class="experienceLevel.color">
                        <span class="text-2xl">{{ experienceLevel.icon }}</span>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Your Level</p>
                        <h3 class="text-2xl font-bold">{{ experienceLevel.text }}</h3>
                    </div>
                </div>
            </section>

            <!-- Motivational Banner -->
            <section class="mb-10">
                <div class="rounded-xl p-6 shadow-lg text-white" :class="motivationalMessage.color">
                    <div class="flex items-center">
                        <span class="text-4xl mr-4">{{ motivationalMessage.icon }}</span>
                        <p class="text-xl font-medium">{{ motivationalMessage.text }}</p>
                    </div>
                </div>
            </section>

            <!-- Improvement Tips -->
            <section class="mb-10">
                <h2 class="text-2xl font-bold mb-6 flex items-center">
                    <span class="mr-2">🚀</span> Growth Tips
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="(tip, index) in [
                        {icon: '💬', title: 'Engage More', desc: 'Respond to comments and messages to build stronger connections.'},
                        {icon: '⏱️', title: 'Be Consistent', desc: 'Post 3-5 times weekly to stay top of mind with your audience.'},
                        {icon: '🎨', title: 'Quality Content', desc: 'Invest in good lighting and editing for professional-looking posts.'},
                        {icon: '❤️', title: 'Be Authentic', desc: 'Share real experiences—your audience values honesty.'}
                    ]" :key="index" class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow hover:-translate-y-1">
                        <div class="text-3xl mb-3">{{ tip.icon }}</div>
                        <h3 class="font-bold mb-2 text-lg">{{ tip.title }}</h3>
                        <p class="text-gray-600">{{ tip.desc }}</p>
                    </div>
                </div>
            </section>

            <!-- Reviews Section -->
            <section class="mb-10">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold flex items-center">
                        <span class="mr-2">📋</span> Customer Reviews
                    </h2>
                    <div class="text-sm text-gray-500">
                        Showing {{ reviews.length }} review{{ reviews.length !== 1 ? 's' : '' }}
                    </div>
                </div>
                <CustomerReview :reviews="reviews" :readonly="true" :reportreview="true" class="bg-white rounded-xl shadow-md overflow-hidden" />
            </section>
            
            <!-- Performance Chart -->
            <section class="bg-white rounded-xl shadow-lg p-6 mb-10">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold flex items-center">
                        <span class="mr-2">📊</span> Performance Trends
                    </h2>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 text-sm bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Monthly</button>
                        <button class="px-3 py-1 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Yearly</button>
                    </div>
                </div>
                <div class="h-80 bg-gray-50 rounded-xl flex items-center justify-center">
                    <div class="text-center p-6">
                        <div class="inline-block bg-white p-4 rounded-full shadow-md mb-4">
                            <span class="text-4xl">📈</span>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700">Rating Analytics</h3>
                        <p class="text-gray-500 mt-1">Visualize your performance trends over time</p>
                        <button class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                            Connect Analytics
                        </button>
                    </div>
                </div>
            </section>
        </main>
    </AuthenticatedLayout>
</template>