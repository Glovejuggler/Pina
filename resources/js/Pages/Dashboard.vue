<script setup>
import BreezeLabel from '@/Components/Label.vue';
import BreezeInput from '@/Components/Input.vue';
import { useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
    items: Number,
    errors: Object,
    dailyReport: Boolean
})

const sellForm = useForm({
    code: '',
    discount: '',
})
</script>

<template>
    <title>
        Dashboard
    </title>

    <div class="max-w-screen-2xl mx-auto py-6 px-8">
        <div @click="$inertia.get(route('items.index'))"
            class="bg-white border rounded-lg w-1/4 p-4 cursor-pointer hover:-translate-y-1 hover:shadow-lg duration-200 ease-in-out">
            <div class="font-bold">
                Items
            </div>
            <div class="flex justify-end font-bold text-7xl">
                {{ items }}
            </div>
        </div>
        <div class="mt-4 flex justify-between w-1/4">
            <p class="font-bold text-lg">Sales</p>
            <a v-if="dailyReport" :href="route('sales.export')"
                class="rounded-full bg-green-600 px-4 py-2 text-white text-xs" v-wave>Generate report</a>
        </div>
        <div class="bg-white border rounded-lg w-1/4 p-4 mt-4">
            <form @submit.prevent="sellForm.post(route('sell'), {
                onSuccess: () => {
                    if (!errors.length) {
                        sellForm.reset()
                    }
                }
            })">
                <div class="flex justify-between">
                    <p class="font-bold">Sell</p>
                    <!-- <button @click="$inertia.get(route('batch.sell'))" type="button"
                        class="bg-gray-800 px-4 rounded-full text-xs text-white" v-wave>Batch
                        sell</button> -->
                </div>
                <div class="mt-4">
                    <BreezeLabel for="code" value="Item code" />
                    <BreezeInput @input="() => {
                        sellForm.code = sellForm.code.toUpperCase()
                        if (sellForm.code) {
                            sellForm.post(route('check'), { preserveState: true })
                        }
                    }" id="code" type="text" class="mt-1 block w-full" v-model="sellForm.code" required />
                    <span v-if="errors.item" class="text-red-500 text-sm">{{ errors.item }}</span>
                    <span v-if="errors.count && sellForm.code" class="text-green-500 text-sm">Stocks left: {{ errors.count
                    }}</span>
                </div>
                <div class="mt-4">
                    <BreezeLabel for="discount" value="Discount" />
                    <BreezeInput id="discount" type="number" class="mt-1 block w-full" v-model="sellForm.discount" />
                </div>
                <button :disabled="errors.item" :class="{ 'opacity-25': errors.item }"
                    class="px-4 py-2 mt-4 w-full text-xs bg-gray-800 rounded-lg text-white" v-wave>
                    Save
                </button>
            </form>
        </div>
    </div>
</template>
