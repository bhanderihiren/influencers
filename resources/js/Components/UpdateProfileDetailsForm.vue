<script setup>
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
// Update these imports to point to the correct locations
import TextArea from '@/Components/TextArea.vue';
import SelectInput from '@/Components/SelectInput.vue';

const props = defineProps({
    gender: String,
    contactNo: String,
    bio: String
});

const form = useForm({
    gender: props.gender || '',
    contact_no: props.contactNo || '',
    bio: props.bio || ''
});

const submit = () => {
    form.put(route('profile.details.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <div>
            <InputLabel for="gender" value="Gender" />
            <SelectInput
                id="gender"
                v-model="form.gender"
                class="mt-1 block w-full"
                :options="[
                    { value: '', text: 'Select gender' },
                    { value: 'male', text: 'Male' },
                    { value: 'female', text: 'Female' },
                    { value: 'other', text: 'Other' }
                ]"
            />
            <InputError class="mt-2" :message="form.errors.gender" />
        </div>

        <div>
            <InputLabel for="contact_no" value="Contact Number" />
            <TextInput
                id="contact_no"
                v-model="form.contact_no"
                type="tel"
                class="mt-1 block w-full"
                autocomplete="tel"
            />
            <InputError class="mt-2" :message="form.errors.contact_no" />
        </div>

        <div>
            <InputLabel for="bio" value="About/Bio" />
            <TextArea
                id="bio"
                v-model="form.bio"
                class="mt-1 block w-full"
                rows="4"
            />
            <InputError class="mt-2" :message="form.errors.bio" />
        </div>

        <div class="flex items-center gap-4">
            <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0">
                
                <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Saved.</p>
            </Transition>
        </div>
    </form>
</template>