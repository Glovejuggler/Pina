<script setup>
import { Head } from '@inertiajs/inertia-vue3'

const props = defineProps({
    items: Number
})
</script>

<template>
    <Head title="Dashboard" />

    <div class="max-w-screen-2xl mx-auto py-6">
        <div @click="$inertia.get(route('items.index'))"
            class="bg-white rounded-lg w-1/4 p-4 cursor-pointer hover:-translate-y-1 hover:shadow-lg duration-200 ease-in-out">
            <div class="font-bold">
                Items
            </div>
            <div class="flex justify-end font-bold text-7xl">
                {{ items }}
            </div>
        </div>
    </div>
</template>
