<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    tasks: Array,
});

const openMenuId = ref(null);

const toggleMenu = (id) => {
    openMenuId.value = openMenuId.value === id ? null : id;
};
const closeMenu = () => {
    openMenuId.value = null;
};

const updateStatus = (taskId, status) => {
    router.patch(`/tasks/${taskId}/status`, { status }, {
        onSuccess: () => closeMenu(),
    });
};

const priorityColors = {
    low: 'bg-gray-100 text-gray-600',
    medium: 'bg-yellow-50 text-yellow-700',
    high: 'bg-red-50 text-red-700',
};

const totalCount = computed(() => props.tasks.length);
const doneCount = computed(() => props.tasks.filter(t => t.status === 'completed').length);
const progressPercent = computed(() => {
    if (totalCount.value === 0) return 0;
    return Math.round((doneCount.value / totalCount.value) * 100);
});

const stageCounts = computed(() => {
    const tasks = props.tasks;
    return {
        todo: tasks.filter(t => t.status === 'assigned').length,
        inProgress: tasks.filter(t => t.status === 'accepted' || t.status === 'in_progress').length,
        done: tasks.filter(t => t.status === 'completed').length,
        cantDo: tasks.filter(t => t.status === 'cannot_do').length,
    };
});
</script>

<template>
    <AppLayout>
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tasks</h1>
                <p class="text-gray-500 mt-1">Track and manage all farm tasks.</p>
            </div>
            <Link
                href="/tasks/create"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Task
            </Link>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Total</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ totalCount }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">To Be Done</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ stageCounts.todo }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">In Progress</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ stageCounts.inProgress }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Done</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ stageCounts.done }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Can't Do</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ stageCounts.cantDo }}</p>
            </div>
        </div>

        <div class="mb-6 bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-medium text-gray-600">Overall Progress</span>
                <span class="text-sm font-bold text-gray-900">{{ progressPercent }}%</span>
            </div>
            <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                <div
                    class="h-full bg-blue-600 rounded-full transition-all duration-700"
                    :style="{ width: progressPercent + '%' }"
                ></div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Task</th>
                        <th class="text-left p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Activity</th>
                        <th class="text-left p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Assigned To</th>
                        <th class="text-left p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Priority</th>
                        <th class="text-left p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="text-left p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Due Date</th>
                        <th class="text-right p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="task in tasks"
                        :key="task.id"
                        class="border-t hover:bg-gray-50 transition-colors"
                    >
                        <td class="p-4">
                            <Link
                                :href="`/tasks/${task.id}`"
                                class="font-semibold text-gray-900 hover:text-blue-600 transition-colors"
                            >
                                {{ task.title }}
                            </Link>
                            <p class="text-sm text-gray-400 mt-0.5 line-clamp-1">
                                {{ task.description }}
                            </p>
                        </td>
                        <td class="p-4 text-sm text-gray-600">
                            {{ task.activity?.name }}
                        </td>
                        <td class="p-4 text-sm text-gray-700">
                            {{ task.assignee?.name || 'Unassigned' }}
                        </td>
                        <td class="p-4">
                            <span
                                :class="['inline-flex items-center px-2 py-0.5 rounded text-xs font-medium', priorityColors[task.priority] || 'bg-gray-100 text-gray-600']"
                            >
                                {{ task.priority }}
                            </span>
                        </td>
                        <td class="p-4">
                            <div class="relative">
                                <span
                                    :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ring-1 ring-inset cursor-pointer hover:opacity-80 transition-opacity', task.status_color]"
                                    @click="toggleMenu(task.id)"
                                >
                                    {{ task.status_label }}
                                </span>
                                <div
                                    v-if="openMenuId === task.id"
                                    class="absolute right-0 top-8 z-20 w-44 bg-white rounded-lg shadow-lg border border-gray-200 py-1"
                                    @click.stop
                                >
                                    <button
                                        v-if="task.next_status"
                                        @click="updateStatus(task.id, task.next_status)"
                                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors w-full text-left"
                                    >
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        {{ task.next_status_label }}
                                    </button>
                                    <button
                                        v-if="task.status !== 'assigned'"
                                        @click="updateStatus(task.id, 'assigned')"
                                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors w-full text-left"
                                    >
                                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                        Reset To Do
                                    </button>
                                    <button
                                        v-if="task.status !== 'cannot_do'"
                                        @click="updateStatus(task.id, 'cannot_do')"
                                        class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors w-full text-left"
                                    >
                                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Can't Do
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-sm text-gray-700">
                            {{ task.due_date ? new Date(task.due_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—' }}
                        </td>
                        <td class="p-4 text-right">
                            <Link
                                :href="`/tasks/${task.id}`"
                                class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors"
                            >
                                View
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div
                v-if="tasks.length === 0"
                class="p-12 text-center"
            >
                <svg class="w-10 h-10 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p class="text-gray-500">No tasks yet</p>
            </div>
        </div>
    </AppLayout>
</template>
