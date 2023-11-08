<script setup>
import BreezeLabel from '@/Components/Label.vue';
import BreezeInput from '@/Components/Input.vue';
import { useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
    items: Number,
    current_inventory: Number,
    errors: Object,
    dailyReport: Boolean,
    item: Object
})

const sellForm = useForm({
    code: '',
    priceSold: '',
    date: new Date().toISOString().slice(0, 10)
})

const currency = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'PHP'
})
</script>

<template>
    <div class="max-w-screen-2xl mx-auto py-6 px-8">
        <div class="flex space-x-2">
            <div @click="$inertia.get(route('items.index'))"
                class="bg-white border rounded-lg w-1/4 p-4 cursor-pointer hover:-translate-y-1 hover:shadow-lg duration-200 ease-in-out">
                <div class="font-bold">
                    Items
                </div>
                <div class="flex justify-end font-bold text-7xl">
                    {{ items }}
                </div>
            </div>

            <div class="bg-white border rounded-lg w-1/4 p-4">
                <div class="font-bold">
                    Current Inventory
                </div>
                <div class="flex justify-end font-bold text-7xl">
                    {{ current_inventory.toLocaleString() }}
                </div>
            </div>
        </div>
        <div class="mt-4 flex justify-between w-1/4">
            <p class="font-bold text-lg">Sales</p>
            <a v-if="dailyReport" :href="route('sales.export')"
                class="rounded-full bg-green-600 px-4 py-2 text-white text-xs" v-wave>Generate report</a>
        </div>
        <div class="flex space-x-2">
            <div class="bg-white border rounded-lg w-1/4 p-4 mt-4">
                <form @submit.prevent="sellForm.post(route('sell'), {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: () => {
                        sellForm.reset('code', 'priceSold')
                        sellForm.code = ''
                    }
                })">
                    <div class="flex justify-between">
                        <p class="font-bold">Sell</p>
                        <!-- <button @click="$inertia.get(route('batch.sell'))" type="button"
                        class="bg-gray-800 px-4 rounded-full text-xs text-white" v-wave>Batch
                        sell</button> -->
                        <input type="date" v-model="sellForm.date" class="text-xs rounded-full border-gray-300">
                    </div>
                    <div class="mt-4">
                        <BreezeLabel for="code" value="Item code" />
                        <BreezeInput @input="() => {
                            sellForm.code = sellForm.code.toUpperCase()
                            if (sellForm.code) {
                                sellForm.post(route('check'), { preserveState: true })
                            } else {
                                item = null
                            }
                        }" id="code" type="text" class="mt-1 block w-full" v-model="sellForm.code" required />
                        <span v-if="errors.item" class="text-red-500 text-sm">{{ errors.item }}</span>
                    </div>
                    <div class="mt-4">
                        <BreezeLabel for="price" value="Price sold" />
                        <BreezeInput id="price" type="number" class="mt-1 block w-full" v-model="sellForm.priceSold"
                            required />
                        <span v-if="sellForm.priceSold > item?.item?.price" class="text-red-500 text-sm">Higher than item's
                            selling price</span>
                    </div>
                    <button :disabled="!item || item?.item?.tally.number < 1 || errors.item || !sellForm.priceSold"
                        :class="{ 'opacity-25': !item || item?.item?.tally.number < 1 || errors.item || !sellForm.priceSold }"
                        class="px-4 py-2 mt-4 w-full text-xs bg-gray-800 rounded-lg text-white" v-wave>
                        Save
                    </button>
                </form>
            </div>
            <div v-if="item?.item" class="bg-white border rounded-lg w-1/4 p-4 mt-4 h-min">
                <p class="font-bold">{{ item.item.brand }}</p>
                <p class="text-sm">{{ item.item.description }}</p>
                <span v-if="item?.item && sellForm.code" class="text-sm"
                    :class="item?.item.tally.number < 1 ? 'text-red-500' : 'text-green-500'">
                    Stocks left: {{ item?.item.tally.number }}</span>
                <p>Price: <span class="font-bold">{{ currency.format(item.item.price) }}</span></p>
                <p>Cost: <span class="font-bold">{{ currency.format(item.item.cost) }}</span></p>
            </div>
        </div>

    </div>
</template>
