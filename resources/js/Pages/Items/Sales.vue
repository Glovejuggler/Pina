<script setup>
import { ref, watch } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import { pickBy } from 'lodash';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    sales: Object,
    filters: Object
})

const currency = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'PHP'
})

const date = new Intl.DateTimeFormat('en-us', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
})

const form = ref({
    period: props.filters.period || '',
    date: props.filters.date || null,
})

watch(
    form,
    value => {
        Inertia.get('/sales', pickBy(value), {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    },
    {
        deep: true
    }
)
</script>

<template>
    <div class="py-6 mx-auto max-w-5xl">
        <div class="flex justify-between mb-3">
            <div class="font-bold text-lg">Sales</div>
            <div class="flex space-x-2">
                <input v-if="form.period === ''" type="date"
                    class="rounded-lg w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm"
                    v-model="form.date">
                <select v-model="form.period"
                    class="block rounded-lg text-sm text-gray-700 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm w-full">
                    <option value="">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                </select>
            </div>
        </div>
        <template v-if="sales">
            <template v-for="(gsales, grouping) in sales.data">
                <div class="flex space-x-3 py-2">
                    <p class="text-gray-800">{{ grouping }}</p>
                    <a :href="route('sales.export', [form.period || 'daily', gsales[0].created_at])"
                        class="rounded-full bg-green-600 px-4 inline-flex items-center text-white text-xs" v-wave>Generate
                        report</a>
                </div>
                <div v-for="sale in gsales" class="bg-white rounded-lg p-4 mb-2 grid grid-cols-3 border">
                    <div class="flex space-x-3">
                        <div class="flex h-10 w-10 bg-slate-100 rounded-md justify-center items-center">
                            <img v-if="sale.item.image" :src="`../storage/${sale.item.image}`"
                                class="max-w-10 max-h-10 object-contain" alt="">
                        </div>
                        <div>
                            <p class="font-semibold">{{ sale.item.name }}</p>
                            <p class="text-sm">{{ sale.item.code }}</p>
                            <p class="text-sm text-slate-500 line-clamp-1 block">{{ sale.item.description }}</p>
                        </div>
                    </div>
                    <div>
                        <p>{{ currency.format(sale.item.price - sale.discount) }}</p>
                        <p class="text-xs">Cost: {{ currency.format(sale.item.cost) }}</p>
                        <p v-if="sale.discount" class="text-xs line-through opacity-60">{{ currency.format(sale.item.price)
                        }}
                        </p>
                        <p v-if="sale.discount" class="text-xs opacity-60">Discount: {{ currency.format(sale.discount)
                        }}<span class="ml-2">-{{
    Math.round((sale.discount / sale.item.price) * 100) }}%</span></p>
                    </div>
                    <div class="text-sm">
                        <p>Date encoded: {{ date.format(new Date(sale.item.updated_at)) }}</p>
                        <p>Date sold: {{ date.format(new Date(sale.created_at)) }}</p>
                    </div>
                </div>
            </template>
        </template>
        <div v-if="sales.data.length < 1" class="flex justify-center">
            No sales data
        </div>
        <div class="mt-4 flex justify-end px-4">
            <Pagination :links="sales.links" />
        </div>
    </div>
</template>