<script setup>
defineProps({
    status: String,
    perPage: Number,
    total: Number,
    from: Number,
    to: Number,
});

const emit = defineEmits(["update:status", "update:perPage"]);
</script>

<template>
    <div
        class="flex flex-col md:flex-row md:items-center justify-between mb-3 gap-3"
    >
        <!-- Status Filter -->
        <div class="flex items-center gap-2">
            <label class="text-xs font-medium text-gray-700"
                >Filter Status:</label
            >
            <select
                :value="status"
                @input="$emit('update:status', $event.target.value)"
                class="border p-2 rounded text-sm min-w-[120px] focus:ring-2 focus:ring-blue-400"
            >
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
        </div>

        <div class="flex items-center gap-4">
            <div class="text-sm text-gray-600" v-if="total">
                Showing {{ from }} to {{ to }} of {{ total }} results
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Per page:</label>
                <select
                    :value="perPage"
                    @input="
                        $emit('update:perPage', parseInt($event.target.value))
                    "
                    class="border p-1 rounded text-sm"
                >
                    <option :value="5">5</option>
                    <option :value="10">10</option>
                    <option :value="15">15</option>
                    <option :value="25">25</option>
                </select>
            </div>
        </div>
    </div>
</template>
