<script setup>
import { ref } from "vue";

const emit = defineEmits(["create"]);

const form = ref({
    title: "",
    description: "",
    status: "pending",
    due_date: "",
});

const submit = () => {
    emit("create", { ...form.value });
};

const reset = () => {
    form.value = {
        title: "",
        description: "",
        status: "pending",
        due_date: "",
    };
};

defineExpose({ reset });
</script>

<template>
    <div class="mb-6 p-4 bg-gray-50 border rounded-lg shadow-sm">
        <h2 class="text-lg font-semibold mb-3 text-gray-700">Add New Task</h2>
        <form
            @submit.prevent="submit"
            class="grid grid-cols-12 gap-3 items-end"
        >
            <!-- Title: 3 cols -->
            <div class="col-span-12 md:col-span-6 lg:col-span-3">
                <label class="block text-xs font-medium text-gray-600 mb-1"
                    >Title</label
                >
                <input
                    v-model="form.title"
                    type="text"
                    required
                    class="w-full border p-2 rounded text-sm focus:ring-2 focus:ring-blue-400"
                    placeholder="Task Title"
                />
            </div>

            <!-- Description: 4 cols -->
            <div class="col-span-12 md:col-span-6 lg:col-span-4">
                <label class="block text-xs font-medium text-gray-600 mb-1"
                    >Description</label
                >
                <input
                    v-model="form.description"
                    type="text"
                    class="w-full border p-2 rounded text-sm focus:ring-2 focus:ring-blue-400"
                    placeholder="Optional description"
                />
            </div>

            <!-- Status: 2 cols -->
            <div class="col-span-6 md:col-span-3 lg:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1"
                    >Status</label
                >
                <select
                    v-model="form.status"
                    class="w-full border p-2 rounded text-sm focus:ring-2 focus:ring-blue-400"
                >
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <!-- Due Date: 2 cols -->
            <div class="col-span-6 md:col-span-3 lg:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1"
                    >Due Date</label
                >
                <input
                    v-model="form.due_date"
                    type="date"
                    class="w-full border p-2 rounded text-sm focus:ring-2 focus:ring-blue-400"
                />
            </div>

            <!-- Button: 1 col -->
            <div class="col-span-12 lg:col-span-1 flex">
                <button
                    type="submit"
                    class="w-full bg-blue-600 text-white px-2 py-2 rounded hover:bg-blue-700 transition text-sm font-medium h-[38px] flex items-center justify-center whitespace-nowrap"
                >
                    Add
                </button>
            </div>
        </form>
    </div>
</template>
