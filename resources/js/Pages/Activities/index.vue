<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    activities: Array,
});

const openMenuId = ref(null);

const toggleMenu = (id) => {
    openMenuId.value = openMenuId.value === id ? null : id;
};

const closeMenu = () => {
    openMenuId.value = null;
};

const handleDelete = (id) => {
    if (confirm('Are you sure you want to delete this activity?')) {
        router.delete(`/activities/${id}`, {
            onSuccess: () => closeMenu(),
        });
    }
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const progressPercent = (activity) => {
    const total = (activity.completed_tasks || 0) + (activity.in_progress_tasks || 0) + (activity.pending_tasks || 0) + (activity.cant_do_tasks || 0);
    if (total === 0) return 0;
    return Math.round((activity.completed_tasks / total) * 100);
};

const totalTasksCount = (activity) => {
    return (activity.completed_tasks || 0) + (activity.in_progress_tasks || 0) + (activity.pending_tasks || 0) + (activity.cant_do_tasks || 0);
};
</script>

<template>
    <AppLayout>
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Activities</h1>
                <p class="text-gray-500 mt-1">Manage business activities and the tasks associated with them.</p>
            </div>
            <Link
                href="/activities/create"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Activity
            </Link>
        </div>

        <div
            v-if="activities.length === 0"
            class="text-center py-16 bg-white rounded-xl border border-gray-200"
        >
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <p class="text-gray-500 text-lg">No activities yet</p>
            <p class="text-gray-400 mt-1">Create your first activity to get started.</p>
            <Link
                href="/activities/create"
                class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors"
            >
                Create Activity
            </Link>
        </div>

        <div
            v-else
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
            <div
                v-for="activity in activities"
                :key="activity.id"
                class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow relative"
            >
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex-1 min-w-0 pr-4">
                            <h2 class="text-lg font-bold text-gray-900 truncate">{{ activity.name }}</h2>
                            <p class="text-gray-500 mt-1 text-sm line-clamp-2">{{ activity.description }}</p>
                        </div>
                        <div class="relative">
                            <button
                                @click.stop="toggleMenu(activity.id)"
                                class="p-1.5 rounded-md hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
                            </button>
                            <div
                                v-if="openMenuId === activity.id"
                                class="absolute right-0 top-8 z-20 w-40 bg-white rounded-lg shadow-lg border border-gray-200 py-1"
                                @click.stop
                            >
                                <Link
                                    :href="`/activities/${activity.id}`"
                                    @click="closeMenu"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                                >
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    View
                                </Link>
                                <Link
                                    :href="`/activities/${activity.id}/edit`"
                                    @click="closeMenu"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                                >
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </Link>
                                <button
                                    @click="handleDelete(activity.id)"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors w-full text-left"
                                >
                                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="text-xs text-gray-500 font-medium">
                                {{ activity.completed_tasks || 0 }} of {{ totalTasksCount(activity) }} tasks completed
                            </span>
                            <span class="text-xs font-semibold text-gray-700">{{ progressPercent(activity) }}%</span>
                        </div>
                        <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                            <div
                                class="h-full bg-blue-600 rounded-full transition-all duration-500"
                                :style="{ width: progressPercent(activity) + '%' }"
                            ></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-2 mt-5">
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-900">{{ totalTasksCount(activity) }}</p>
                            <p class="text-xs text-gray-400">Total</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-900">{{ activity.pending_tasks || 0 }}</p>
                            <p class="text-xs text-gray-400">Pending</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-900">{{ activity.in_progress_tasks || 0 }}</p>
                            <p class="text-xs text-gray-400">Active</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-green-600">{{ activity.completed_tasks || 0 }}</p>
                            <p class="text-xs text-gray-400">Done</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-5 pt-4 border-t border-gray-100">
                        <div class="flex items-center gap-2 text-xs text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>{{ activity.creator?.name || 'Unknown' }}</span>
                            <span class="mx-1">·</span>
                            <span>{{ formatDate(activity.created_at) }}</span>
                        </div>
                        <Link
                            :href="`/activities/${activity.id}`"
                            class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors"
                        >
                            View
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
