<script setup>
const props = defineProps({
    sales: Object,
    headDate: String,
    info: Object,
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
    <div class="py-6 mx-auto max-w-5xl px-8">
        <div class="flex justify-between">
            <p class="text-lg font-semibold items-center flex cursor-pointer hover:opacity-50 duration-150 ease-in-out"
                @click="$inertia.get(route('items.sales'))">
                <i class="bx bx-left-arrow-alt text-2xl mr-4"></i>{{
                    headDate }} Sales
            </p>

            <div>
                <a :href="route('sales.export', [info.period ?? 'daily', info.date])"
                    class="rounded-full px-4 py-1 bg-green-500 text-white text-xs" v-wave>Download report</a>
            </div>
        </div>
        <div v-for="sale in sales" class="bg-white rounded-lg p-4 mb-2 grid grid-cols-3 border">
            <div class="flex space-x-3">
                <div class="flex h-10 w-10 bg-slate-100 rounded-md justify-center items-center">
                    <img v-if="sale.item.image" :src="`../storage/${sale.item.image}`"
                        class="max-w-10 max-h-10 object-contain" alt="">
                    <i v-else class="bx bx-image-alt text-xl text-slate-900"></i>
                </div>
                <div>
                    <p class="font-semibold">{{ sale.item.brand }}</p>
                    <p class="text-sm">{{ sale.item.description }}</p>
                    <p class="text-sm">{{ sale.item.code }}</p>
                </div>
            </div>
            <div>
                <p>{{ currency.format(sale.item.price - sale.discount) }}</p>
                <p class="text-xs">Cost: {{ currency.format(sale.item.cost) }}</p>
                <p class="text-xs">Selling price: {{ currency.format(sale.item.price) }}</p>
                <p v-if="sale.discount && sale.discount > 0" class="text-xs line-through opacity-60">{{
                    currency.format(sale.item.price)
                }}
                </p>
                <p v-if="sale.discount && sale.discount > 0" class="text-xs opacity-60">Discount: {{
                    currency.format(sale.discount)
                }}<span class="ml-2">-{{
    Math.round((sale.discount / sale.item.price) * 100) }}%</span></p>
            </div>
            <div class="text-sm">
                <p>Date encoded: {{ date.format(new Date(sale.item.updated_at)) }}</p>
                <p>Date sold: {{ date.format(new Date(sale.created_at)) }}</p>
            </div>
        </div>
    </div>
</template>