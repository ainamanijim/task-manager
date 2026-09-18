<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    activities: Array,
    users: Array,
    activityId: [String, Number],
});

const form = useForm({
    title: '',
    description: '',
    activity_id: props.activityId || '',
    assigned_to: '',
    priority: 'medium',
    due_date: '',
});

const submit = () => {
    form.post('/tasks');
};
</script>

<template>

    <AppLayout>

        <h1 class="text-3xl font-bold mb-8">
            Create Task
        </h1>

        <form
            @submit.prevent="submit"
            class="bg-white p-6 rounded shadow
                   max-w-2xl"
        >

            <div class="mb-5">

                <label class="block mb-2">
                    Task Title
                </label>

                <input
                    v-model="form.title"
                    type="text"
                    class="w-full border rounded p-3"
                    placeholder="Spray tomatoes"
                />

                <p
                    v-if="form.errors.title"
                    class="text-red-500"
                >
                    {{ form.errors.title }}
                </p>

            </div>


            <div class="mb-5">

                <label class="block mb-2">
                    Activity
                </label>

                <select
                    v-model="form.activity_id"
                    class="w-full border rounded p-3"
                >

                    <option value="">
                        Select activity
                    </option>

                    <option
                        v-for="activity in activities"
                        :key="activity.id"
                        :value="activity.id"
                    >
                        {{ activity.name }}
                    </option>

                </select>

            </div>


            <div class="mb-5">

                <label class="block mb-2">
                    Assign To
                </label>

                <select
                    v-model="form.assigned_to"
                    class="w-full border rounded p-3"
                >

                    <option value="">
                        Select person
                    </option>

                    <option
                        v-for="user in users"
                        :key="user.id"
                        :value="user.id"
                    >
                        {{ user.name }}
                    </option>

                </select>

            </div>


            <div
                class="grid grid-cols-2 gap-4 mb-5"
            >

                <div>

                    <label class="block mb-2">
                        Priority
                    </label>

                    <select
                        v-model="form.priority"
                        class="w-full border rounded p-3"
                    >

                        <option value="low">
                            Low
                        </option>

                        <option value="medium">
                            Medium
                        </option>

                        <option value="high">
                            High
                        </option>

                    </select>

                </div>


                <div>

                    <label class="block mb-2">
                        Due Date
                    </label>

                    <input
                        v-model="form.due_date"
                        type="date"
                        class="w-full border rounded p-3"
                    />

                </div>

            </div>


            <div class="mb-5">

                <label class="block mb-2">
                    Description
                </label>

                <textarea
                    v-model="form.description"
                    rows="5"
                    class="w-full border rounded p-3"
                    placeholder="Describe what needs to be done..."
                ></textarea>

            </div>


            <button
                type="submit"
                class="bg-blue-600 text-white
                       px-5 py-3 rounded"
                :disabled="form.processing"
            >
                Create Task
            </button>

        </form>

    </AppLayout>

</template>