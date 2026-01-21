<script setup>
defineProps({
    tasks: Array,
    loading: Boolean,
    perPage: Number,
    statusFilter: String,
});

const emit = defineEmits(["delete", "update-status"]);
</script>

<template>
    <div class="relative">
        <div
            v-if="loading"
            class="absolute inset-0 bg-white/60 flex items-center justify-center z-10 transition-opacity"
        >
            <span class="text-lg font-semibold text-gray-700">Loading...</span>
        </div>

        <div class="overflow-x-auto" :class="{ 'opacity-50': loading }">
            <table
                class="w-full border-collapse border border-gray-200 table-fixed"
            >
                <thead class="bg-gray-100">
                    <tr>
                        <th
                            class="border p-2 text-left font-semibold text-gray-600 w-1/2"
                        >
                            Title & Description
                        </th>
                        <th
                            class="border p-2 text-left font-semibold text-gray-600 w-32"
                        >
                            Status
                        </th>
                        <th
                            class="border p-2 text-left font-semibold text-gray-600 w-32"
                        >
                            Due Date
                        </th>
                        <th
                            class="border p-2 text-center font-semibold text-gray-600 w-24"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="task in tasks"
                        :key="task.id"
                        class="hover:bg-gray-50 transition h-24"
                    >
                        <td class="border p-3 align-top">
                            <div
                                class="font-bold text-gray-800 truncate"
                                :title="task.title"
                            >
                                {{ task.title }}
                            </div>
                            <div
                                class="text-sm text-gray-500 mt-1 line-clamp-2"
                                :title="task.description"
                            >
                                {{ task.description }}
                            </div>
                        </td>
                        <td class="border p-2 align-top">
                            <select
                                :value="task.status"
                                @change="
                                    emit('update-status', {
                                        ...task,
                                        status: $event.target.value,
                                    })
                                "
                                class="border p-1 rounded text-sm w-full"
                                :class="{
                                    'bg-yellow-50 text-yellow-700 border-yellow-200':
                                        task.status === 'pending',
                                    'bg-blue-50 text-blue-700 border-blue-200':
                                        task.status === 'in_progress',
                                    'bg-green-50 text-green-700 border-green-200':
                                        task.status === 'completed',
                                }"
                            >
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </td>
                        <td class="border p-2 text-gray-700 align-top">
                            {{ task.due_date }}
                        </td>
                        <td class="border p-2 text-center align-top">
                            <button
                                @click="emit('delete', task.id)"
                                class="text-red-500 hover:text-red-700 p-1"
                            >
                                🗑️
                            </button>
                        </td>
                    </tr>
                    <!-- Spacer rows -->
                    <tr
                        v-if="tasks.length < perPage"
                        v-for="i in perPage - tasks.length"
                        :key="'empty-' + i"
                        class="h-24"
                    >
                        <td colspan="4" class="border p-2"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
