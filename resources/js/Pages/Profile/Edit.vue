<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const currentTab = ref('profile');
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto p-6 space-y-6">
            <div class="flex gap-4 border-b pb-2">
                <button
                    class="px-4 py-2 text-sm font-medium"
                    :class="currentTab === 'profile' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
                    @click="currentTab = 'profile'"
                >
                    Profile Information
                </button>
                <button
                    class="px-4 py-2 text-sm font-medium"
                    :class="currentTab === 'password' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
                    @click="currentTab = 'password'"
                >
                    Update Password
                </button>
                <button
                    class="px-4 py-2 text-sm font-medium"
                    :class="currentTab === 'delete' ? 'border-b-2 border-red-600 text-red-600' : 'text-gray-600'"
                    @click="currentTab = 'delete'"
                >
                    Delete Account
                </button>
            </div>

            <div v-if="currentTab === 'profile'">
                <UpdateProfileInformationForm />
            </div>

            <div v-else-if="currentTab === 'password'">
                <UpdatePasswordForm />
            </div>

            <div v-else-if="currentTab === 'delete'">
                <DeleteUserForm />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
