<script setup>
const props = defineProps({
    sales: Object,
    headDate: String,
    info: Object,
    totalNet: Number,
    totalMarkup: Number
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
    <div class="py-6 mx-auto max-w-screen-2xl px-8">
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
        <div class="flex space-x-2">
            <div class="w-9/12">
                <div class="bg-white rounded-lg border shadow-sm overflow-x-auto">
                    <table class="text-left w-full text-gray-500 text-sm rounded-lg">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Code
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Brand
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Description
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Cost
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Selling price
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Net
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Markup
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="sale in sales" class="bg-white border-b">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ sale.item.code }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ sale.item.brand }}
                                </td>
                                <td class="px-6 py-4 max-w-md">
                                    {{ sale.item.description }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ currency.format(sale.item.cost) }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ currency.format(sale.item.price) }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ currency.format(sale.net) }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ currency.format(sale.markup) }}
                                </td>
                            </tr>
                            <tr class="bg-white font-bold">
                                <td colspan="5" class="text-center">
                                    Total
                                </td>
                                <td class="px-6">{{ currency.format(totalNet) }}</td>
                                <td class="px-6">{{ currency.format(totalMarkup) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg border w-3/12 h-min">
                <p class="font-bold">Summary</p>
                <p>Total net: <span class="font-bold">{{ currency.format(totalNet) }}</span></p>
                <p>Total markup: <span class="font-bold">{{ currency.format(totalMarkup) }}</span></p>
            </div>
        </div>
    </div>
</template>