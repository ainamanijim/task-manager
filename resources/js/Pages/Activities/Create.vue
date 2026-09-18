<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    activities: Array,
});

const form = useForm({
    name: '',
    description: '',
    icon: '',
});

const errors = computed(() => form.errors);

const submit = () => {
    form.post('/activities', {
        onSuccess: () => {
            form.reset();
        },
    });
};

const cancel = () => {
    form.reset();
};

const iconOptions = ['🌾', '🌱', '🚜', '🌳', '🌻', '🐄', '🐔', '🥕', '🍎', '🌾', '🪣', '🔧'];

const selectIcon = (icon) => {
    form.icon = icon;
};
</script>

<template>
    <AppLayout>
        <div class="max-w-2xl">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Create Activity</h1>
                <p class="text-gray-500 mt-1">Add a new business activity to manage.</p>
            </div>

            <form
                @submit.prevent="submit"
                class="bg-white rounded-xl border border-gray-200 shadow-sm p-6"
            >
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Activity Name
                    </label>
                    <input
                        v-model="form.name"
                        type="text"
                        :class="[
                            'w-full border rounded-lg p-3 transition-colors',
                            form.errors.name
                                ? 'border-red-300 focus:ring-red-500 focus:border-red-500'
                                : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500',
                        ]"
                        placeholder="e.g. Crop Harvesting"
                    />
                    <p
                        v-if="form.errors.name"
                        class="text-red-500 mt-1 text-sm"
                    >
                        {{ form.errors.name }}
                    </p>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea
                        v-model="form.description"
                        rows="4"
                        :class="[
                            'w-full border rounded-lg p-3 transition-colors',
                            form.errors.description
                                ? 'border-red-300 focus:ring-red-500 focus:border-red-500'
                                : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500',
                        ]"
                        placeholder="Describe this activity..."
                    ></textarea>
                    <p
                        v-if="form.errors.description"
                        class="text-red-500 mt-1 text-sm"
                    >
                        {{ form.errors.description }}
                    </p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Activity Icon <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="icon in iconOptions"
                            :key="icon"
                            type="button"
                            @click="selectIcon(icon)"
                            :class="[
                                'w-10 h-10 rounded-lg border flex items-center justify-center text-xl transition-colors',
                                form.icon === icon
                                    ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500'
                                    : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50',
                            ]"
                        >
                            {{ icon }}
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-6 py-2.5 rounded-lg font-medium transition-colors"
                    >
                        {{ form.processing ? 'Creating...' : 'Create Activity' }}
                    </button>
                    <button
                        type="button"
                        @click="cancel"
                        :disabled="form.processing"
                        class="border border-gray-300 text-gray-700 px-6 py-2.5 rounded-lg font-medium hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <Link
                    href="/activities"
                    class="text-sm text-gray-500 hover:text-gray-700 transition-colors"
                >
                    ← Back to Activities
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
