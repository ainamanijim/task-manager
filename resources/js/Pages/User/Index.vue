<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    users: Array,
});
</script>

<template>

    <AppLayout>

        <h1 class="text-3xl font-bold mb-8">
            People
        </h1>

        <div
            class="grid grid-cols-1 md:grid-cols-3
                   gap-6"
        >

            <div
                v-for="user in users"
                :key="user.id"
                class="bg-white p-6 rounded shadow"
            >

                <h2 class="text-xl font-bold">
                    {{ user.name }}
                </h2>

                <p class="text-gray-500">
                    {{ user.email }}
                </p>

                <p class="mt-3 capitalize">
                    {{ user.role }}
                </p>

                <Link
                    :href="`/users/${user.id}`"
                    class="inline-block mt-4
                           text-blue-600"
                >
                    View Tasks →
                </Link>

            </div>

        </div>

    </AppLayout>

</template>