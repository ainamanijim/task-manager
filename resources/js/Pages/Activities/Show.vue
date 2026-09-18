<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    activity: Object,
});

const tasks = computed(() => props.activity.tasks || []);

const summaryCounts = computed(() => {
    const t = tasks.value;
    return {
        total: t.length,
        assigned: t.filter(task => task.status === 'assigned').length,
        accepted: t.filter(task => task.status === 'accepted').length,
        inProgress: t.filter(task => task.status === 'in_progress').length,
        completed: t.filter(task => task.status === 'completed').length,
        cannotDo: t.filter(task => task.status === 'cannot_do').length,
        overdue: t.filter(task => {
            if (task.status === 'completed' || !task.due_date) return false;
            return new Date(task.due_date) < new Date();
        }).length,
    };
});

const hasOverdue = computed(() => summaryCounts.value.overdue > 0);

const statusColors = {
    assigned: 'bg-blue-50 text-blue-700 ring-blue-600',
    accepted: 'bg-purple-50 text-purple-700 ring-purple-600',
    in_progress: 'bg-amber-50 text-amber-700 ring-amber-600',
    completed: 'bg-green-50 text-green-700 ring-green-600',
    cannot_do: 'bg-red-50 text-red-700 ring-red-600',
};

const priorityColors = {
    low: 'bg-gray-100 text-gray-600',
    medium: 'bg-yellow-50 text-yellow-700',
    high: 'bg-red-50 text-red-700',
};

const formatDate = (dateStr) => {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};

const isOverdue = (task) => {
    if (task.status === 'completed' || !task.due_date) return false;
    return new Date(task.due_date) < new Date();
};

const confirmDelete = () => {
    if (confirm('Are you sure you want to delete this activity and all its tasks?')) {
        router.delete(`/activities/${props.activity.id}`, {
            onSuccess: () => {
                router.get('/activities');
            },
        });
    }
};

const assigneeProgress = computed(() => {
    const map = {};
    tasks.value.forEach(task => {
        const name = task.assignee?.name || 'Unassigned';
        if (!map[name]) {
            map[name] = { total: 0, completed: 0, inProgress: 0, pending: 0, overdue: 0 };
        }
        map[name].total++;
        if (task.status === 'completed') map[name].completed++;
        else if (task.status === 'in_progress' || task.status === 'accepted') map[name].inProgress++;
        else map[name].pending++;
        if (isOverdue(task)) map[name].overdue++;
    });
    return Object.entries(map).map(([name, data]) => ({ name, ...data }));
});
</script>

<template>
    <AppLayout>
        <div class="max-w-6xl">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ activity.name }}</h1>
                    <p class="text-gray-500 mt-1">{{ activity.description }}</p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <Link
                        :href="`/activities/${activity.id}/edit`"
                        class="inline-flex items-center gap-1.5 border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </Link>
                    <button
                        @click="confirmDelete"
                        class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3 mb-8">
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Total</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ summaryCounts.total }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Assigned</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ summaryCounts.assigned }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Accepted</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ summaryCounts.accepted }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">In Progress</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ summaryCounts.inProgress }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Completed</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ summaryCounts.completed }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Can't Do</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ summaryCounts.cannotDo }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm" :class="hasOverdue ? 'border-red-300' : ''">
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Overdue</p>
                    <p class="text-2xl font-bold mt-1" :class="hasOverdue ? 'text-red-600' : 'text-gray-900'">{{ summaryCounts.overdue }}</p>
                </div>
            </div>

            <div
                v-if="assigneeProgress.length > 0"
                class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm mb-6"
            >
                <h2 class="text-lg font-bold text-gray-900 mb-4">Who's Doing What</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        v-for="person in assigneeProgress"
                        :key="person.name"
                        class="border border-gray-100 rounded-lg p-4 hover:bg-gray-50 transition-colors"
                    >
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                                {{ person.name.charAt(0) }}
                            </div>
                            <span class="font-semibold text-gray-900">{{ person.name }}</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <span
                                v-if="person.pending > 0"
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700"
                            >
                                {{ person.pending }} To Do
                            </span>
                            <span
                                v-if="person.inProgress > 0"
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700"
                            >
                                {{ person.inProgress }} Active
                            </span>
                            <span
                                v-if="person.completed > 0"
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-50 text-green-700"
                            >
                                {{ person.completed }} Done
                            </span>
                            <span
                                v-if="person.overdue > 0"
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-50 text-red-700"
                            >
                                {{ person.overdue }} Overdue
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Tasks</h2>
                    <Link
                        :href="`/tasks/create?activity_id=${activity.id}`"
                        class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Task
                    </Link>
                </div>

                <div
                    v-if="tasks.length === 0"
                    class="bg-white rounded-xl border border-gray-200 p-12 text-center"
                >
                    <svg class="w-10 h-10 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-gray-500">No tasks yet</p>
                </div>

                <div
                    v-else
                    class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden"
                >
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Task</th>
                                    <th class="text-left p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Assigned</th>
                                    <th class="text-left p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Priority</th>
                                    <th class="text-left p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Due Date</th>
                                    <th class="text-left p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                    <th class="text-left p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Created By</th>
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
                                        <p class="text-sm text-gray-400 mt-0.5 line-clamp-1">{{ task.description }}</p>
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
                                    <td class="p-4 text-sm text-gray-700">
                                        <span :class="isOverdue(task) ? 'text-red-600 font-medium' : ''">
                                            {{ formatDate(task.due_date) }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <span
                                            :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset', statusColors[task.status] || 'bg-gray-100 text-gray-600']"
                                        >
                                            {{ task.status_label }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-sm text-gray-700">
                                        {{ task.creator?.name || 'System' }}
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
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
