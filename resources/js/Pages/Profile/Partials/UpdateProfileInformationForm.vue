<script setup>
import { ref, watch } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectBox.vue';
import TextArea from '@/Components/TextArea.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const user = usePage().props.auth.user;
const previewUrl = ref(user.avatar_url || null);

const form = useForm({
    name: user.name,
    email: user.email,
    gender: user.gender || '',
    contact_no: user.contact_no || '',
    bio: user.bio || '',
    avatar: null,
});

watch(() => form.avatar, (newAvatar) => {
    if (newAvatar instanceof File) {
        previewUrl.value = URL.createObjectURL(newAvatar);
    }
});

const submit = () => {
    form.post(route('profile.update'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            if (form.avatar) {
                URL.revokeObjectURL(previewUrl.value);
            }
        },
    });
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Profile Settings</h2>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Manage your account's profile information and settings
            </p>
        </header>

        <form @submit.prevent="submit" class="mt-8 space-y-8">
            <!-- Avatar Section -->
            <div class="space-y-6 p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Profile Picture</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Recommended size: 200x200 pixels (JPG, PNG)
                    </p>
                </div>
                
                <div class="flex items-center gap-6">
                    <div class="relative">
                        <img :src="previewUrl || '/images/default-avatar.png'" 
                             alt="Profile preview"
                             class="h-24 w-24 rounded-full object-cover border-4 border-white dark:border-gray-700 shadow-md">
                    </div>
                    <div class="flex-1">
                        <input
                            id="avatar"
                            type="file"
                            class="block w-full text-sm text-gray-600 dark:text-gray-300
                                   file:mr-4 file:py-2 file:px-4
                                   file:rounded-md file:border-0
                                   file:text-sm file:font-semibold
                                   file:bg-indigo-50 dark:file:bg-gray-700 file:text-indigo-700 dark:file:text-indigo-300
                                   hover:file:bg-indigo-100 dark:hover:file:bg-gray-600"
                            @change="e => form.avatar = e.target.files[0]"
                            accept="image/*"
                        />
                        <InputError class="mt-2" :message="form.errors.avatar" />
                    </div>
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="space-y-6 p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Personal Information</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Update your basic profile details
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <InputLabel for="name" value="Full Name" class="mb-1" />
                        <TextInput
                            id="name"
                            type="text"
                            class="w-full"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                        />
                        <InputError class="mt-1" :message="form.errors.name" />
                    </div>

                    <!-- Email -->
                    <div>
                        <InputLabel for="email" value="Email Address" class="mb-1" />
                        <TextInput
                            id="email"
                            type="email"
                            class="w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                        />
                        <InputError class="mt-1" :message="form.errors.email" />
                    </div>

                    <!-- Gender -->
                    <div>
                        <InputLabel for="gender" value="Gender" class="mb-1" />
                        <SelectInput
                            id="gender"
                            v-model="form.gender"
                            class="w-full"
                            :options="[
                                { value: '', text: 'Select gender' },
                                { value: 'male', text: 'Male' },
                                { value: 'female', text: 'Female' },
                                { value: 'other', text: 'Other' }
                            ]"
                        />
                        <InputError class="mt-1" :message="form.errors.gender" />
                    </div>

                    <!-- Contact Number -->
                    <div>
                        <InputLabel for="contact_no" value="Contact Number" class="mb-1" />
                        <TextInput
                            id="contact_no"
                            v-model="form.contact_no"
                            type="tel"
                            class="w-full"
                            autocomplete="tel"
                        />
                        <InputError class="mt-1" :message="form.errors.contact_no" />
                    </div>
                </div>
            </div>

            <!-- Bio Section -->
            <div class="space-y-6 p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">About You</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Tell others about yourself (max 500 characters)
                    </p>
                </div>
                
                <div>
                    <TextArea
                        id="bio"
                        v-model="form.bio"
                        rows="5"
                        class="w-full"
                        placeholder="Share something about yourself..."
                    />
                    <div class="flex justify-between items-center mt-1">
                        <InputError :message="form.errors.bio" />
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ form.bio ? form.bio.length : 0 }}/500
                        </span>
                    </div>
                </div>
            </div>

            <!-- Email Verification -->
            <div v-if="props.mustVerifyEmail && user.email_verified_at === null" 
                 class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-yellow-400 dark:text-yellow-300 mt-0.5 mr-2 flex-shrink-0" 
                         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300">
                            Your email address is unverified.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="font-medium underline hover:text-yellow-600 dark:hover:text-yellow-400 ml-1"
                            >
                                Click here to re-send the verification email.
                            </Link>
                        </p>
                        <div v-show="props.status === 'verification-link-sent'" 
                             class="mt-2 text-sm text-green-600 dark:text-green-400">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end">
                <PrimaryButton :disabled="form.processing" class="w-full sm:w-auto">
                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                </PrimaryButton>
            </div>
        </form>
    </section>
</template>