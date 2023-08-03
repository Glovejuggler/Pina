<script setup>
const props = defineProps({
    sales: Object
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
</script>

<template>
    <title>
        Sales
    </title>

    <div class="py-6 mx-auto max-w-5xl">
        <div class="flex justify-between mb-3">
            <div class="font-bold text-lg">Sales</div>
            <div>

            </div>
        </div>
        <template v-if="sales">
            <template v-for="(gsales, grouping) in sales">
                <p class="text-gray-800">{{ grouping }}</p>
                <div v-for="sale in gsales" class="bg-white rounded-lg p-4 mb-2 grid grid-cols-3 border">
                    <div class="flex space-x-3">
                        <div class="flex h-24 w-24 bg-slate-100 rounded-md justify-center items-center">
                            <img :src="`../storage/${sale.item.image}`" class="max-w-24 max-h-24 object-contain" alt="">
                        </div>
                        <div>
                            <p class="font-semibold">{{ sale.item.name }}</p>
                            <p class="text-sm">{{ sale.item.code }}</p>
                            <p class="text-sm text-slate-500 line-clamp-1 block">{{ sale.item.description }}</p>
                        </div>
                    </div>
                    <div>
                        <p>{{ currency.format(sale.item.price - sale.discount) }}</p>
                        <p v-if="sale.discount" class="text-xs line-through opacity-60">{{ currency.format(sale.item.price)
                        }}
                        </p>
                        <p v-if="sale.discount" class="text-xs opacity-60">Discount: {{ currency.format(sale.discount)
                        }}<span class="ml-2">{{
    Math.round((sale.discount / sale.item.price) * -100) }}%</span></p>
                    </div>
                    <div class="text-sm">
                        <p>Date encoded: {{ date.format(new Date(sale.item.updated_at)) }}</p>
                        <p>Date sold: {{ date.format(new Date(sale.created_at)) }}</p>
                    </div>
                </div>
            </template>
        </template>
        <div v-if="!sales.data?.length" class="flex justify-center">
            No sales data
        </div>
    </div>
</template>