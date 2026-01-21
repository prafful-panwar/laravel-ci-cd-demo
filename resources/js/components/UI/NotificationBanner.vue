<script setup>
defineProps({
    message: String,
    type: {
        type: String,
        default: "success", // 'success' or 'error'
        validator: (value) => ["success", "error"].includes(value),
    },
});

const emit = defineEmits(["close"]);
</script>

<template>
    <div
        v-if="message"
        class="mb-3 p-3 border rounded flex items-center justify-between animate-fade-in-down"
        :class="
            type === 'success'
                ? 'bg-green-100 text-green-700 border-green-200'
                : 'bg-red-100 text-red-700 border-red-200'
        "
    >
        <span> {{ type === "success" ? "✅" : "⚠️" }} {{ message }} </span>
        <button
            @click="emit('close')"
            class="font-bold"
            :class="type === 'success' ? 'text-green-900' : 'text-red-900'"
        >
            &times;
        </button>
    </div>
</template>
