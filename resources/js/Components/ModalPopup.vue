<template>
    <div v-if="isVisible" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50">
        <div class="bg-white dark:bg-gray-800 text-center p-6 rounded-lg shadow-xl w-80">
            <div v-if="type === 'success'" class="text-green-500">
                <h3 class="font-bold text-xl">Success</h3>
                <p>{{ message }}</p>
            </div>
            <div v-if="type === 'error'" class="text-red-500">
                <h3 class="font-bold text-xl">Error</h3>
                <p>{{ message }}</p>
            </div>
            <button @click="close" class="mt-4 px-6 py-2 bg-blue-500 text-white rounded-lg">
                Close
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

const isVisible = ref(false);
const message = ref("");
const type = ref("success");

const open = (msg, msgType = 'success') => {
    message.value = msg;
    type.value = msgType;
    isVisible.value = true;
};

const close = () => {
    isVisible.value = false;
};
</script>

<style scoped>
/* You can add your custom styles here */
</style>
