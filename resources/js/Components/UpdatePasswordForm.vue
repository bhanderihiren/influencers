<script setup>
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: ''
});

const submit = () => {
    form.put(route('user-password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div>
            <InputLabel for="current_password" value="Current Password" />
            <TextInput
                id="current_password"
                v-model="form.current_password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="current-password"
            />
            <InputError :message="form.errors.current_password" class="mt-2" />
        </div>

        <div>
            <InputLabel for="password" value="New Password" />
            <TextInput
                id="password"
                v-model="form.password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
            />
            <InputError :message="form.errors.password" class="mt-2" />
        </div>

        <div>
            <InputLabel for="password_confirmation" value="Confirm Password" />
            <TextInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
            />
            <InputError :message="form.errors.password_confirmation" class="mt-2" />
        </div>

        <PrimaryButton :disabled="form.processing">Update Password</PrimaryButton>
    </form>
</template>
