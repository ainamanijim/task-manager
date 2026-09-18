<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    task: Object,
});

const updateStatus = (status) => {
    router.patch(`/tasks/${props.task.id}/status`, { status });
};

const progressStages = [
    { key: 'assigned', label: 'To Be Done', color: 'border-blue-400' },
    { key: 'accepted', label: 'Accepted', color: 'border-purple-400' },
    { key: 'in_progress', label: 'In Progress', color: 'border-amber-400' },
    { key: 'completed', label: 'Done', color: 'border-green-400' },
];

const currentStageIndex = computed(() => {
    return progressStages.findIndex(s => s.key === props.task.status);
});

const isStageDone = (index) => {
    if (props.task.status === 'completed') return true;
    return index < currentStageIndex.value;
};

const isStageCurrent = (index) => {
    return index === currentStageIndex.value;
};

const nextAction = computed(() => {
    const map = {
        assigned: { label: "I'll Do It", status: 'accepted', class: 'bg-blue-600 hover:bg-blue-700' },
        accepted: { label: 'Start Task', status: 'in_progress', class: 'bg-yellow-500 hover:bg-yellow-600' },
        in_progress: { label: '✓ Mark as Done', status: 'completed', class: 'bg-green-600 hover:bg-green-700' },
        completed: null,
        cannot_do: null,
    };
    return map[props.task.status] || null;
});

const priorityColors = {
    low: 'bg-gray-100 text-gray-600',
    medium: 'bg-yellow-50 text-yellow-700',
    high: 'bg-red-50 text-red-700',
};
</script>

<template>
    <AppLayout>
        <div class="max-w-3xl">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ task.title }}</h1>
                    <p class="text-gray-500 mt-1">{{ task.activity?.name }}</p>
                </div>
                <Link
                    :href="`/tasks/${task.id}/edit`"
                    class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors inline-flex items-center gap-1.5 self-start"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </Link>
            </div>

            <div
                class="bg-white rounded-xl border border-gray-200 shadow-sm mt-6 p-6"
            >
                <div
                    class="grid grid-cols-2 gap-6"
                >
                    <div>
                        <p class="text-gray-500 text-sm">Assigned To</p>
                        <p class="font-semibold mt-0.5">{{ task.assignee?.name ?? 'Unassigned' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Created By</p>
                        <p class="font-semibold mt-0.5">{{ task.creator?.name ?? 'System' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Priority</p>
                        <p class="mt-0.5">
                            <span
                                :class="['inline-flex items-center px-2 py-0.5 rounded text-xs font-medium', priorityColors[task.priority] || 'bg-gray-100 text-gray-600']"
                            >
                                {{ task.priority }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Due Date</p>
                        <p class="font-semibold mt-0.5">{{ task.due_date ?? 'No due date' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Status</p>
                        <p class="font-semibold mt-0.5">{{ task.status_label }}</p>
                    </div>
                </div>

                <div class="border-t mt-6 pt-6">
                    <h2 class="font-bold mb-1">Description</h2>
                    <p class="text-gray-700">
                        {{ task.description || 'No description.' }}
                    </p>
                </div>

                <!-- Progress Tracker -->
                <div class="border-t mt-6 pt-6">
                    <h2 class="font-bold mb-4">Progress</h2>
                    <div class="flex items-center justify-between mb-2">
                        <template v-for="(stage, i) in progressStages" :key="stage.key">
                            <div class="flex items-center gap-3">
                                <div
                                    :class="[
                                        'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-colors',
                                        isStageDone(i)
                                            ? 'bg-green-500 border-green-500 text-white'
                                            : isStageCurrent(i)
                                                ? `bg-white ${stage.color} ${stage.color.replace('border', 'text')} border-current`
                                                : 'bg-white border-gray-300 text-gray-400',
                                    ]"
                                >
                                    <svg
                                        v-if="isStageDone(i)"
                                        class="w-4 h-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span v-else>{{ i + 1 }}</span>
                                </div>
                                <span
                                    :class="[
                                        'text-sm font-medium whitespace-nowrap',
                                        isStageDone(i) ? 'text-gray-900' : isStageCurrent(i) ? 'text-blue-600' : 'text-gray-400',
                                    ]"
                                >
                                    {{ stage.label }}
                                </span>
                            </div>
                            <div
                                v-if="i < progressStages.length - 1"
                                :class="[
                                    'flex-1 h-1 mx-2 rounded-full transition-colors',
                                    isStageDone(i) ? 'bg-green-400' : 'bg-gray-200',
                                ]"
                            ></div>
                        </template>
                    </div>
                </div>

                <!-- Actions -->
                <div class="border-t mt-6 pt-6">
                    <h2 class="font-bold mb-4">Task Response</h2>
                    <div class="flex flex-wrap gap-3">
                        <button
                            v-if="nextAction"
                            @click="updateStatus(nextAction.status)"
                            :class="['text-white px-5 py-2.5 rounded-lg font-medium transition-colors', nextAction.class]"
                        >
                            {{ nextAction.label }}
                        </button>
                        <button
                            v-if="task.status !== 'assigned'"
                            @click="updateStatus('assigned')"
                            class="border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg font-medium hover:bg-gray-50 transition-colors"
                        >
                            ← Reset To Do
                        </button>
                        <button
                            v-if="task.status !== 'cannot_do'"
                            @click="updateStatus('cannot_do')"
                            class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors"
                        >
                            Can't Do
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
