<script setup>
import { ref, watch } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import { pickBy } from 'lodash';
import { Inertia } from '@inertiajs/inertia';
import { useForm } from '@inertiajs/inertia-vue3';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';

const showEditModal = ref(false)
const showDeleteModal = ref(false)
const editSaleData = ref(null)
const deleteSaleData = ref(null)

const props = defineProps({
    sales: Object,
    filters: Object,
    errors: Object
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

const editForm = useForm({
    dateSold: '',
    priceSold: '',
})

const editSale = (data) => {
    editSaleData.value = data
    editForm.dateSold = new Date(data.created_at).toISOString().split('T')[0]
    editForm.priceSold = data.item.price - data.discount
    showEditModal.value = true
}

const deleteSale = (data) => {
    deleteSaleData.value = data
    showDeleteModal.value = true
}
</script>

<template>
    <div class="py-6 mx-auto max-w-5xl">
        <div class="flex justify-between mb-3">
            <div class="font-bold text-lg">Sales</div>
            <div class="flex space-x-2">
                <input v-if="form.period === ''" type="date" name="date"
                    class="rounded-lg w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm"
                    v-model="form.date">
                <select v-model="form.period" name="period"
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
                    <p @click="Inertia.post(route('sales.view'), {
                        period: form.period || 'daily',
                        date: gsales[0].created_at
                    })"
                        class="rounded-full bg-green-600 px-4 inline-flex items-center text-white text-xs cursor-pointer"
                        v-wave>
                        Report</p>
                </div>
                <div v-for="sale in gsales" class="bg-white rounded-lg p-4 mb-2 grid grid-cols-3 border group relative">
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
                    <div class="absolute top-2 right-2 flex-col flex">
                        <button type="button" v-wave @click="editSale(sale)"
                            class="rounded-lg w-8 h-8 hidden group-hover:flex items-center justify-center bg-slate-300 hover:bg-blue-500 duration-200 ease-in-out mb-1"><i
                                class="bx bxs-edit-alt text-white"></i></button>
                        <button type="button" v-wave @click="deleteSale(sale)"
                            class="rounded-lg w-8 h-8 hidden group-hover:flex items-center justify-center bg-slate-300 hover:bg-red-500 duration-200 ease-in-out"><i
                                class="bx bx-trash-alt text-white"></i></button>
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

    <!-- Edit -->
    <div>
        <Transition enter-active-class="duration-200 ease-out" enter-from-class="transform opacity-0 scale-75"
            enter-to-class="opacity-100 scale-100" leave-active-class="duration-200 ease-out"
            leave-from-class="opacity-100 scale-100" leave-to-class="transform opacity-0 scale-75">
            <div v-if="showEditModal" class="inset-0 fixed z-50 h-screen w-screen flex justify-center items-center"
                @click.self="showEditModal = false">
                <div
                    class="relative bg-white dark:bg-zinc-900 w-full lg:w-1/4 h-auto max-h-[80%] p-6 rounded-lg dark:text-white overflow-auto shadow-2xl">
                    <span class="font-bold text-lg block mb-2">Edit</span>
                    <form @submit.prevent="editForm.post(route('sales.update', editSaleData), {
                        preserveState: true,
                        preserveScroll: true,
                        onSuccess: () => {
                            if (!errors.length) {
                                showEditModal = false
                                editForm.reset()
                            } else {
                                showEditModal = true
                            }
                        }
                    })">
                        <div>
                            <BreezeLabel for="sold" value="Price sold" />
                            <BreezeInput id="sold" type="number" class="mt-1 block w-full" v-model="editForm.priceSold"
                                autofocus />
                        </div>
                        <div class="mt-4">
                            <BreezeLabel for="date" value="Date sold" />
                            <BreezeInput id="date" type="date" class="mt-1 block w-full" v-model="editForm.dateSold"
                                autofocus />
                        </div>
                        <div class="mt-4 flex justify-end space-x-2">
                            <button type="button" class="hover:underline" @click="showEditModal = false">
                                Cancel
                            </button>
                            <button type="submit" :disabled="editForm.processing"
                                :class="{ 'opacity-25': editForm.processing }"
                                class="rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-900 text-white text-sm px-4 py-2">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
        <Transition enter-active-class="duration-200 ease opacity-0" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="duration-200 ease opacity-90" leave-from-class="opacity-90"
            leave-to-class="transform opacity-0" appear>
            <div v-if="showEditModal" class="fixed inset-0 z-40 bg-black/50"></div>
        </Transition>
    </div>

    <!-- Delete -->
    <div>
        <Transition enter-active-class="duration-200 ease-out" enter-from-class="transform opacity-0 scale-75"
            enter-to-class="opacity-100 scale-100" leave-active-class="duration-200 ease-out"
            leave-from-class="opacity-100 scale-100" leave-to-class="transform opacity-0 scale-75">
            <div v-if="showDeleteModal" class="inset-0 fixed z-50 h-screen w-screen flex justify-center items-center"
                @click.self="showDeleteModal = false">
                <div
                    class="relative bg-white dark:bg-zinc-900 w-full lg:w-1/4 h-auto max-h-[80%] p-6 rounded-lg dark:text-white overflow-auto shadow-2xl">
                    <span class="font-bold text-lg block mb-2">Confirmation</span>
                    <div>
                        <p>Are you sure you want to delete this sale record?</p>
                        <p class="text-xs text-red-500">This action cannot be undone.</p>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" class="hover:underline" @click="showDeleteModal = false">Cancel</button>
                        <button type="button" @click="Inertia.delete(route('sales.destroy', deleteSaleData), {
                            onSuccess: () => showDeleteModal = false,
                            preserveScroll: true,
                            preserveState: false,
                        })"
                            class="px-4 py-2 rounded-lg bg-red-500 text-white text-sm hover:bg-red-700 active:bg-red-900"
                            v-wave>Delete</button>
                    </div>
                </div>
            </div>
        </Transition>
        <Transition enter-active-class="duration-200 ease opacity-0" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="duration-200 ease opacity-90" leave-from-class="opacity-90"
            leave-to-class="transform opacity-0" appear>
            <div v-if="showDeleteModal" class="fixed inset-0 z-40 bg-black/50"></div>
        </Transition>
    </div>
</template>