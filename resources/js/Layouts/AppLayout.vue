<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const user = usePage().props.user;
</script>

<template>

    <div class="min-h-screen bg-gray-100">

        <nav class="bg-white border-b">

            <div
                class="max-w-7xl mx-auto px-6 py-4 flex justify-between"
            >

                <div class="flex items-center gap-6">

                    <Link
                        href="/dashboard"
                        class="font-bold text-xl"
                    >
                        Task Manager
                    </Link>

                    <Link href="/dashboard">
                        Dashboard
                    </Link>

                    <Link href="/tasks">
                        Tasks
                    </Link>

                    <Link href="/activities">
                        Activities
                    </Link>

                    <Link
                        v-if="user?.role === 'admin'"
                        href="/users"
                    >
                        People
                    </Link>

                </div>

                <div>

                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                    >
                        Logout
                    </Link>

                </div>

            </div>

        </nav>

        <main
            class="max-w-7xl mx-auto px-6 py-8"
        >
            <slot />
        </main>

    </div>

</template>
