<script setup>
defineProps({
    links: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['change-page']);

const handleClick = (link) => {
    if (link.url && !link.active) {
        emit('change-page', link);
    }
};
</script>

<template>
    <div v-if="links.length > 3" class="flex items-center justify-center gap-1 mt-2 py-2">
        <button
            v-for="(link, idx) in links"
            :key="idx"
            class="px-2 py-1 border rounded text-xs min-w-[28px]"
            :class="[
                link.active ? 'bg-blue-600 text-white border-blue-600' : 'text-gray-600 hover:bg-gray-50',
                !link.url ? 'opacity-50 cursor-not-allowed' : '',
            ]"
            :disabled="!link.url"
            @click="handleClick(link)"
        >
            {{ link.label.replace('&laquo;', '«').replace('&raquo;', '»') }}
        </button>
    </div>
</template>
