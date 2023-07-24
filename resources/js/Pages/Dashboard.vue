<script setup>
import { Head } from '@inertiajs/inertia-vue3';

const props = defineProps({
    items: Number
})
</script>

<template>
    <Head title="Dashboard" />

    <div class="max-w-screen-2xl mx-auto py-6">
        <div class="bg-white rounded-lg w-1/4 p-4">
            <div class="font-bold">
                Items
            </div>
            <div class="flex justify-end font-bold text-7xl">
                {{ items }}
            </div>
        </div>
    </div>
</template>
