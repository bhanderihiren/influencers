<script setup>
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import DangerButton from '@/Components/DangerButton.vue';

const form = useForm({
    password: ''
});

const confirmDelete = () => {
    if (confirm('Are you sure you want to delete your account?')) {
        form.delete(route('profile.destroy'), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <div class="space-y-6">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Once your account is deleted, all of its resources and data will be permanently deleted.
        </p>

        <form @submit.prevent="confirmDelete" class="space-y-4">
            <div>
                <InputLabel for="password" value="Confirm Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                />
                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <DangerButton :disabled="form.processing">
                Delete Account
            </DangerButton>
        </form>
    </div>
</template>