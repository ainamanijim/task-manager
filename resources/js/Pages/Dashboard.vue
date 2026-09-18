<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    statistics: Object,
    recentTasks: Array,
    workerWorkloads: Array,
});

const workerStatusColor = (status) => {
    const map = {
        todo: 'bg-blue-50 text-blue-700',
        inProgress: 'bg-amber-50 text-amber-700',
        done: 'bg-green-50 text-green-700',
        cantDo: 'bg-red-50 text-red-700',
    };
    return map[status] || 'bg-gray-50 text-gray-700';
};
</script>

<template>
    <AppLayout>

        <div
            class="flex justify-between items-center mb-8"
        >

            <div>

                <h1 class="text-3xl font-bold">
                    Dashboard
                </h1>

                <p class="text-gray-500">
                    Overview of your work
                </p>

            </div>

            <Link
                href="/tasks/create"
                class="bg-blue-600 text-white
                       px-4 py-2 rounded"
            >
                + New Task
            </Link>

        </div>


        <!-- Statistics -->

        <div
            class="grid grid-cols-2 md:grid-cols-4
                   lg:grid-cols-7 gap-4 mb-8"
        >

            <div class="bg-white p-5 rounded shadow">
                <p class="text-gray-500">
                    Total
                </p>

                <p class="text-3xl font-bold">
                    {{ statistics.total }}
                </p>
            </div>

            <div class="bg-white p-5 rounded shadow">
                <p class="text-gray-500">
                    Assigned
                </p>

                <p class="text-3xl font-bold">
                    {{ statistics.assigned }}
                </p>
            </div>

            <div class="bg-white p-5 rounded shadow">
                <p class="text-gray-500">
                    Accepted
                </p>

                <p class="text-3xl font-bold">
                    {{ statistics.accepted }}
                </p>
            </div>

            <div class="bg-white p-5 rounded shadow">
                <p class="text-gray-500">
                    In Progress
                </p>

                <p class="text-3xl font-bold">
                    {{ statistics.in_progress }}
                </p>
            </div>

            <div class="bg-white p-5 rounded shadow">
                <p class="text-gray-500">
                    Completed
                </p>

                <p class="text-3xl font-bold">
                    {{ statistics.completed }}
                </p>
            </div>

            <div class="bg-white p-5 rounded shadow">
                <p class="text-gray-500">
                    Can't Do
                </p>

                <p class="text-3xl font-bold">
                    {{ statistics.cannot_do }}
                </p>
            </div>

            <div class="bg-white p-5 rounded shadow">
                <p class="text-gray-500">
                    Overdue
                </p>

                <p class="text-3xl font-bold">
                    {{ statistics.overdue }}
                </p>
            </div>

        </div>


        <!-- Worker Workloads -->

        <div
            v-if="workerWorkloads && workerWorkloads.length > 0"
            class="bg-white rounded shadow mb-8"
        >
            <div class="p-5 border-b">
                <h2 class="text-xl font-bold">
                    Worker Workloads
                </h2>
            </div>

            <div
                v-for="worker in workerWorkloads"
                :key="worker.id"
                class="p-5 border-b last:border-b-0 hover:bg-gray-50 transition-colors"
            >
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                            {{ worker.name.charAt(0) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ worker.name }}</p>
                            <p class="text-sm text-gray-400">{{ worker.total }} tasks</p>
                        </div>
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        <span
                            v-if="worker.assigned > 0"
                            :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium', workerStatusColor('todo')]"
                        >
                            {{ worker.assigned }} To Do
                        </span>
                        <span
                            v-if="worker.in_progress > 0"
                            :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium', workerStatusColor('inProgress')]"
                        >
                            {{ worker.in_progress }} Active
                        </span>
                        <span
                            v-if="worker.completed > 0"
                            :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium', workerStatusColor('done')]"
                        >
                            {{ worker.completed }} Done
                        </span>
                        <span
                            v-if="worker.cannot_do > 0"
                            :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium', workerStatusColor('cantDo')]"
                        >
                            {{ worker.cannot_do }}
                        </span>
                        <span
                            v-if="worker.overdue > 0"
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700"
                        >
                            {{ worker.overdue }} Overdue
                        </span>
                    </div>
                </div>
            </div>
        </div>


        <!-- Recent Tasks -->

        <div class="bg-white rounded shadow">

            <div class="p-5 border-b">

                <h2 class="text-xl font-bold">
                    Recent Tasks
                </h2>

            </div>

            <div
                v-for="task in recentTasks"
                :key="task.id"
                class="p-5 border-b
                       flex justify-between"
            >

                <div>

                    <Link
                        :href="`/tasks/${task.id}`"
                        class="font-semibold"
                    >
                        {{ task.title }}
                    </Link>

                    <p class="text-sm text-gray-500">
                        {{ task.activity?.name }}
                    </p>

                </div>

                <div class="text-right">

                    <p>
                        {{ task.assignee?.name }}
                    </p>

                    <p class="text-sm text-gray-500">
                        {{ task.status }}
                    </p>

                </div>

            </div>

        </div>

    </AppLayout>
</template>
