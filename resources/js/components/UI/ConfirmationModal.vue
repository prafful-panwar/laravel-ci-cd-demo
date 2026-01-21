<script setup>
defineProps({
    show: Boolean,
    title: {
        type: String,
        default: 'Confirm Action',
    },
    message: {
        type: String,
        default: 'Are you sure you want to proceed?',
    },
    confirmText: {
        type: String,
        default: 'Confirm',
    },
    cancelText: {
        type: String,
        default: 'Cancel',
    },
    type: {
        type: String,
        default: 'danger', // 'danger' or 'primary'
        validator: (value) => ['danger', 'primary'].includes(value),
    },
});

const emit = defineEmits(['close', 'confirm']);
</script>

<template>
    <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white p-5 rounded-lg shadow-xl max-w-sm w-full mx-4 border border-gray-100 animate-fade-in-down">
            <h3 class="text-lg font-bold text-gray-800 mb-2">{{ title }}</h3>
            <p class="text-gray-600 mb-6">{{ message }}</p>
            <div class="flex justify-end gap-3">
                <button class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded transition" @click="emit('close')">
                    {{ cancelText }}
                </button>
                <button
                    class="px-4 py-2 text-white rounded transition"
                    :class="type === 'danger' ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'"
                    @click="emit('confirm')"
                >
                    {{ confirmText }}
                </button>
            </div>
        </div>
    </div>
</template>
