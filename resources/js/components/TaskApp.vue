<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Components
import NotificationBanner from './UI/NotificationBanner.vue';
import ConfirmationModal from './UI/ConfirmationModal.vue';
import Pagination from './UI/Pagination.vue';
import TaskForm from './Tasks/TaskForm.vue';
import TaskList from './Tasks/TaskList.vue';
import TaskFilters from './Tasks/TaskFilters.vue';

// Data
const tasks = ref([]);
const loading = ref(false);

// UI State
const statusFilter = ref('');
const successMessage = ref('');
const errorMessage = ref('');
const tasksContainer = ref(null);
const confirmDeleteId = ref(null);
const taskFormRef = ref(null);

// Pagination State
const pagination = ref({
    current_page: 1,
    per_page: 10,
    total: 0,
    from: 0,
    to: 0,
    links: [],
});

// Helper for Notifications
const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const showSuccess = (msg) => {
    successMessage.value = msg;
    scrollToTop();
    setTimeout(() => {
        successMessage.value = '';
    }, 3000);
};

const showError = (msg) => {
    errorMessage.value = msg;
    scrollToTop();
    setTimeout(() => {
        errorMessage.value = '';
    }, 5000);
};

// API Interactions
const fetchTasks = async () => {
    loading.value = true;
    try {
        const params = {
            page: pagination.value.current_page,
            per_page: pagination.value.per_page,
        };
        if (statusFilter.value) {
            params.status = statusFilter.value;
        }

        const { data } = await axios.get('/api/tasks', { params });

        tasks.value = data.data || [];

        if (data.meta) {
            pagination.value = {
                ...pagination.value,
                ...data.meta,
                links: data.meta.links || [],
            };
        }
    } catch (err) {
        console.error('Error fetching tasks', err);
        showError('Failed to load tasks.');
    } finally {
        loading.value = false;
    }
};

const handlePageChange = (link) => {
    if (!link.url || link.active) return;
    const url = new URL(link.url);
    const page = url.searchParams.get('page');
    if (page) {
        pagination.value.current_page = parseInt(page);
        fetchTasks();
    }
};

const createTask = async (taskData) => {
    try {
        await axios.post('/api/tasks', taskData);
        // Reset form
        if (taskFormRef.value) {
            taskFormRef.value.reset();
        }

        pagination.value.current_page = 1;
        statusFilter.value = ''; // Reset filter to see the new task
        await fetchTasks();
        showSuccess('Task created successfully!');
    } catch (err) {
        if (err.response?.status === 422) {
            const errors = Object.values(err.response.data.errors).flat().join(', ');
            showError(errors);
        } else {
            showError(err.response?.data?.message || 'Failed to create task');
        }
    }
};

const updateStatus = async (task) => {
    // Optimistically update local state so UI/Colors change immediately
    const index = tasks.value.findIndex((t) => t.id === task.id);
    if (index !== -1) {
        // Replace the entire object to ensure reactivity triggers downstream
        tasks.value[index] = { ...tasks.value[index], status: task.status };
    }

    try {
        await axios.put(`/api/tasks/${task.id}`, { ...task });
        showSuccess('Task status updated!');
    } catch {
        showError('Failed to update status');
        await fetchTasks(); // Revert on failure
    }
};

const deleteTask = (id) => {
    confirmDeleteId.value = id;
};

const executeDelete = async () => {
    const id = confirmDeleteId.value;
    confirmDeleteId.value = null; // Close modal immediately
    try {
        await axios.delete(`/api/tasks/${id}`);
        showSuccess('Task deleted successfully!');
        await fetchTasks();
        if (tasks.value.length === 0 && pagination.value.current_page > 1) {
            pagination.value.current_page--;
            await fetchTasks();
        }
    } catch {
        showError('Failed to delete task');
    }
};

onMounted(() => {
    console.info('[TaskApp] mounted');
    fetchTasks();
});
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 bg-white shadow-lg rounded-lg mt-6">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-4">Task Manager - CI/CD</h1>

        <!-- Notifications -->
        <NotificationBanner :message="successMessage" type="success" @close="successMessage = ''" />
        <NotificationBanner :message="errorMessage" type="error" @close="errorMessage = ''" />

        <!-- Modals -->
        <ConfirmationModal
            :show="!!confirmDeleteId"
            title="Delete Task?"
            message="Are you sure you want to remove this task? This action cannot be undone."
            confirm-text="Delete"
            @close="confirmDeleteId = null"
            @confirm="executeDelete"
        />

        <!-- Create Task Section -->
        <TaskForm ref="taskFormRef" @create="createTask" />

        <!-- Task List Section -->
        <div ref="tasksContainer" class="scroll-mt-4">
            <h2 class="text-lg font-semibold mb-2 text-gray-700">All Tasks</h2>

            <TaskFilters
                :status="statusFilter"
                :per-page="pagination.per_page"
                :total="pagination.total"
                :from="pagination.from"
                :to="pagination.to"
                @update:status="
                    (val) => {
                        statusFilter = val;
                        pagination.current_page = 1;
                        fetchTasks();
                    }
                "
                @update:per-page="
                    (val) => {
                        pagination.per_page = val;
                        pagination.current_page = 1;
                        fetchTasks();
                    }
                "
            />

            <!-- Loading/Empty State -->
            <div v-if="loading && tasks.length === 0" class="text-center py-10 text-gray-500">Loading tasks...</div>

            <div
                v-else-if="tasks.length === 0"
                class="text-center py-10 bg-gray-50 rounded text-gray-500 border border-dashed"
            >
                <span v-if="statusFilter">No tasks found with status "{{ statusFilter }}".</span>
                <span v-else>No tasks found. Create one above!</span>
            </div>

            <!-- Table & Pagination -->
            <div v-else>
                <TaskList
                    :tasks="tasks"
                    :loading="loading"
                    :per-page="pagination.per_page"
                    :status-filter="statusFilter"
                    @delete="deleteTask"
                    @update-status="updateStatus"
                />

                <Pagination :links="pagination.links" @change-page="handlePageChange" />
            </div>
        </div>
    </div>
</template>
