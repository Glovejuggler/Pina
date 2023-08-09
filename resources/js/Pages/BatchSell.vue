<script setup>
import BreezeLabel from '@/Components/Label.vue';
import BreezeInput from '@/Components/Input.vue';
import { useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
    report: Object
})

const form = useForm({
    items: []
})

const addItem = () => {
    form.items.push({
        code: '',
        discount: '',
    })
}

const removeItem = (index) => {
    form.items.splice(index, 1)
}
</script>

<template>
    <div class="mx-auto max-w-2xl px-8 py-6">
        <p class="font-bold text-lg text-gray-800 mb-3">Batch sell</p>
        <form @submit.prevent="form.post(route('sell.batch'))">
            <div class="bg-white p-2 rounded-lg border mb-2" v-for="(item, index) in form.items" :key="index">
                <div class="flex justify-between">
                    <span class="text-xs">Item {{ index + 1 }}</span>
                    <i @click.stop="removeItem(index)"
                        class="bx bx-x h-5 w-5 inline-flex items-center justify-center rounded-full text-red-500 hover:bg-black/20"></i>
                </div>
                <div class="grid grid-cols-2 gap-1">
                    <div>
                        <BreezeLabel :for="`${index}.code`" value="Code" />
                        <BreezeInput @input="item.code = item.code.toUpperCase()" :id="`${index}.code`" type="text"
                            v-model="item.code" class="w-full" required />
                    </div>
                    <div>
                        <BreezeLabel :for="`${index}.discount`" value="Discount" />
                        <BreezeInput :id="`${index}.discount`" type="number" v-model="item.discount" class="w-full" />
                    </div>
                </div>
            </div>
            <button @click="addItem" type="button"
                class="bg-green-700 flex justify-center items-center rounded-lg p-1 cursor-pointer w-full text-white"
                v-wave>
                <p>Add item</p>
            </button>
            <button v-if="form.items.length" type="submit"
                class="bg-gray-800 text-white p-1 flex w-full justify-center items-center rounded-lg mt-1 cursor-pointer"
                v-wave>
                Sell items
            </button>
        </form>
    </div>
</template>