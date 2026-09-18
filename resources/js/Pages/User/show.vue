<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    user: Object,
});
</script>

<template>

    <AppLayout>

        <h1 class="text-3xl font-bold">
            {{ user.name }}
        </h1>

        <p class="text-gray-500">
            {{ user.email }}
        </p>

        <div class="mt-8">

            <h2 class="text-xl font-bold mb-4">
                Assigned Tasks
            </h2>

            <div
                v-if="user.assigned_tasks.length"
                class="bg-white rounded shadow"
            >

                <div
                    v-for="task in user.assigned_tasks"
                    :key="task.id"
                    class="p-5 border-b
                           flex justify-between"
                >

                    <div>

                        <Link
                            :href="`/tasks/${task.id}`"
                            class="font-semibold
                                   text-blue-600"
                        >
                            {{ task.title }}
                        </Link>

                        <p class="text-sm text-gray-500">
                            {{ task.activity?.name }}
                        </p>

                    </div>

                    <span>
                        {{ task.status }}
                    </span>

                </div>

            </div>

            <p
                v-else
                class="text-gray-500"
            >
                No tasks assigned.
            </p>

        </div>

    </AppLayout>

</template>