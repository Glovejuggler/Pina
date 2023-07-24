<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import Pagination from '@/Components/Pagination.vue';
import BreezeLabel from '@/Components/Label.vue';
import BreezeInput from '@/Components/Input.vue';
import { Inertia } from '@inertiajs/inertia';
import pickBy from 'lodash/pickBy';

const showNewItemModal = ref(false)
const showEditItemModal = ref(false)
const showDeleteItemModal = ref(false)
const editItem = ref('')
const deleteItem = ref('')
const imgTmp = ref('')

const props = defineProps({
    items: Object,
    errors: Object,
    filters: Object
})

const newform = useForm({
    image: '',
    name: '',
    description: '',
    price: '',
    code: '',
})

const editForm = useForm({
    image: '',
    name: '',
    description: '',
    price: '',
    code: '',
})

const updateNewImage = () => {
    if (newform.image) {
        let reader = new FileReader();
        reader.readAsDataURL(newform.image)

        reader.onload = (e) => {
            imgTmp.value = e.target.result
        }
    }
}

const updateEditImage = () => {
    if (editForm.image) {
        let reader = new FileReader();
        reader.readAsDataURL(editForm.image)

        reader.onload = (e) => {
            imgTmp.value = e.target.result
        }
    }
}

const setCount = (item, count) => {
    Inertia.post(route('items.setCount'), {
        item: item,
        count: Number(count)
    })
}

const itemEdit = (item) => {
    editItem.value = item
    editForm.name = item.name
    editForm.description = item.description
    editForm.price = item.price
    editForm.code = item.code
    showEditItemModal.value = true
}

const itemDelete = (item) => {
    deleteItem.value = item
    showDeleteItemModal.value = true
}

const form = ref({
    search: props.filters.search
})

watch(
    form,
    value => {
        Inertia.get('/items', pickBy(value), {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    },
    {
        deep: true
    }
)

watch(
    showNewItemModal,
    value => {
        if (!value) {
            imgTmp.value = null
            newform.reset()
        }
    }
)

watch(
    showEditItemModal,
    value => {
        if (!value) {
            imgTmp.value = null
            editForm.reset()
        }
    }
)

watch(
    newform,
    value => {
        if (!value.image) imgTmp.value = null
    }
)

watch(
    editForm,
    value => {
        if (!value.image) imgTmp.value = null
    }
)
</script>

<template>
    <title>
        Items
    </title>

    <div class="max-w-screen-2xl py-6 mx-auto">
        <div class="flex justify-between">
            <span class="font-bold text-lg">Items</span>
            <div>
                <label class="relative block">
                    <i class='bx bx-search dark:text-white/20 absolute inset-y-0 left-0 flex items-center pl-3'></i>
                    <input v-model="form.search"
                        class="duration-300 ease-in-out placeholder:italic placeholder:text-slate-400 dark:placeholder:text-gray-500 dark:text-white/80 block bg-white dark:bg-zinc-900 w-96 border-slate-300 dark:border-slate-300/20 rounded-md py-2 pl-9 pr-3 shadow-sm focus:border-indigo-300 focus:ring-indigo-200 focus:ring focus:ring-opacity-50 sm:text-sm"
                        placeholder="Search..." type="text" name="search" />
                </label>
            </div>
            <div>
                <button @click="showNewItemModal = true" class="rounded-full px-4 py-2 bg-gray-800 text-white text-xs"
                    v-wave>Add</button>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border mt-4">
            <div v-if="items.data.length" class="relative overflow-x-auto rounded-lg">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Code
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Description
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Price
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Count
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items.data" class="bg-white border-b">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ item.code }}
                            </th>
                            <td class="px-6 py-4">
                                {{ item.name }}
                            </td>
                            <td class="px-6 py-4 max-w-md">
                                {{ item.description }}
                            </td>
                            <td class="px-6 py-4">
                                ₱ {{ item.price.toLocaleString() }}
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <button
                                        class="rounded-lg w-5 bg-slate-300 hover:bg-slate-500 hover:text-white active:bg-slate-700 active:text-white"
                                        @click="setCount(item, item.tally.number - 1)">-</button>
                                    <input class="mx-2 w-12 rounded-lg text-xs" type="text" :value="item.tally.number"
                                        @change="setCount(item, $event.target.value)">
                                    <button
                                        class="rounded-lg w-5 bg-slate-300 hover:bg-slate-500 hover:text-white active:bg-slate-700 active:text-white"
                                        @click="setCount(item, item.tally.number + 1)">+</button>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-x-2">
                                    <button @click="itemEdit(item)"
                                        class="rounded-lg bg-green-600 hover:bg-green-700 active:bg-green-900 text-white text-xs px-4 py-2">
                                        Edit
                                    </button>
                                    <button @click="itemDelete(item)"
                                        class="rounded-lg bg-red-600 hover:bg-red-700 active:bg-red-900 text-white text-xs px-4 py-2">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="flex items-center justify-center p-5">
                No data
            </div>
        </div>
        <div class="mt-4 flex justify-end px-4">
            <Pagination :links="items.links" />
        </div>
    </div>

    <!-- New Item Modal -->
    <div>
        <Transition enter-active-class="duration-200 ease-out" enter-from-class="transform opacity-0 scale-75"
            enter-to-class="opacity-100 scale-100" leave-active-class="duration-200 ease-out"
            leave-from-class="opacity-100 scale-100" leave-to-class="transform opacity-0 scale-75">
            <div v-if="showNewItemModal" class="inset-0 fixed z-50 h-screen w-screen flex justify-center items-center"
                @click.self="showNewItemModal = false">
                <div
                    class="relative bg-white dark:bg-zinc-900 w-full lg:w-auto h-auto max-h-[80%] p-6 rounded-lg dark:text-white overflow-auto">
                    <span class="font-bold text-lg block mb-2">New Item</span>
                    <form @submit.prevent="newform.post(route('items.store'), {
                        onSuccess: () => {
                            if (!errors.length) {
                                showNewItemModal = false
                                newform.reset()
                            } else {
                                showNewItemModal = true
                            }
                        },
                        preserveState: true,
                        preserveScroll: true,
                    })">
                        <div class="flex items-center">
                            <div v-if="!newform.image" onclick="document.querySelector('#newItemFile').click();"
                                class="h-64 w-64 rounded-lg bg-slate-300 hover:bg-slate-400 duration-300 ease-in-out cursor-pointer m-2 flex items-center justify-center">
                                <i class='bx bx-image-add text-5xl text-white'></i>
                            </div>
                            <div v-if="imgTmp" onclick="document.querySelector('#newItemFile').click();"
                                class="group bg-black flex items-center justify-center relative cursor-pointer">
                                <i @click.stop="imgTmp = null, newform.image = null"
                                    class="bx bx-x h-5 w-5 inline-flex items-center justify-center rounded-full bg-black/50 text-white absolute right-2 top-2 z-50 group-hover:text-black group-hover:bg-white duration-200 ease-in-out hover:scale-125"></i>
                                <i
                                    class="bx bx-image-add text-5xl text-white absolute group-hover:opacity-100 opacity-0 z-30 duration-300 ease-in-out"></i>
                                <img :src="imgTmp"
                                    class="max-w-64 max-h-64 group-hover:opacity-50 duration-300 ease-in-out">
                            </div>
                            <input id="newItemFile" type="file" accept="image/*"
                                @input="newform.image = $event.target.files[0]" @change="updateNewImage" hidden>
                            <div class="w-96 m-2">
                                <div>
                                    <BreezeLabel for="name" value="Name" />
                                    <BreezeInput id="name" type="text" class="mt-1 block w-full" v-model="newform.name"
                                        required autofocus />
                                </div>

                                <div class="mt-4">
                                    <BreezeLabel for="description" value="Description" />
                                    <BreezeInput id="description" type="text" class="mt-1 block w-full"
                                        v-model="newform.description" />
                                </div>

                                <div class="mt-4">
                                    <BreezeLabel for="price" value="Price (₱)" />
                                    <BreezeInput id="price" type="number" class="mt-1 block w-full"
                                        v-model="newform.price" />
                                </div>

                                <div class="mt-4">
                                    <BreezeLabel for="code" value="Code" />
                                    <BreezeInput id="code" type="text" class="mt-1 block w-full" v-model="newform.code" />
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-2">
                            <button @click="showNewItemModal = false" type="button"
                                class="hover:underline dark:text-white/80">Cancel</button>
                            <button type="submit" :disabled="newform.processing"
                                :class="{ 'opacity-25': newform.processing }"
                                class="px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-900 text-sm rounded-lg">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
        <Transition enter-active-class="duration-200 ease opacity-0" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="duration-200 ease opacity-90" leave-from-class="opacity-90"
            leave-to-class="transform opacity-0" appear>
            <div v-if="showNewItemModal" class="fixed inset-0 z-40 bg-black/50 backdrop-blur-md"></div>
        </Transition>
    </div>

    <!-- Edit Item Modal -->
    <div>
        <Transition enter-active-class="duration-200 ease-out" enter-from-class="transform opacity-0 scale-75"
            enter-to-class="opacity-100 scale-100" leave-active-class="duration-200 ease-out"
            leave-from-class="opacity-100 scale-100" leave-to-class="transform opacity-0 scale-75">
            <div v-if="showEditItemModal" class="inset-0 fixed z-50 h-screen w-screen flex justify-center items-center"
                @click.self="showEditItemModal = false">
                <div
                    class="relative bg-white dark:bg-zinc-900 w-full lg:w-auto h-auto max-h-[80%] p-6 rounded-lg dark:text-white overflow-auto">
                    <span class="font-bold text-lg block mb-2">Edit Item</span>
                    <form enctype="multipart/form-data" @submit.prevent="editForm.post(route('items.update', editItem), {
                        onSuccess: () => {
                            if (!errors.length) {
                                showEditItemModal = false
                                editForm.reset()
                            } else {
                                showEditItemModal = true
                            }
                        },
                        preserveState: true,
                        preserveScroll: true,
                    })">
                        <div class="flex items-center">
                            <div v-if="!editItem.image" onclick="document.querySelector('#editItemFile').click();"
                                class="h-64 w-64 rounded-lg bg-slate-300 hover:bg-slate-400 duration-300 ease-in-out cursor-pointer m-2 flex items-center justify-center">
                                <i class='bx bx-image-add text-5xl text-white'></i>
                            </div>
                            <div onclick="document.querySelector('#editItemFile').click();"
                                class="group bg-black flex items-center justify-center relative cursor-pointer">
                                <i @click.stop="imgTmp = null" v-if="imgTmp"
                                    class="bx bx-x h-5 w-5 inline-flex items-center justify-center rounded-full bg-black/50 text-white absolute right-2 top-2 z-50 group-hover:text-black group-hover:bg-white duration-200 ease-in-out hover:scale-125"></i>
                                <i
                                    class="bx bx-image-add text-5xl text-white absolute group-hover:opacity-100 opacity-0 z-30 duration-300 ease-in-out"></i>
                                <img :src="imgTmp" v-if="imgTmp"
                                    class="max-w-64 max-h-64 group-hover:opacity-50 duration-300 ease-in-out">
                                <img :src="`../storage/${editItem.image}`" v-if="!imgTmp"
                                    class="max-w-64 max-h-64 group-hover:opacity-50 duration-300 ease-in-out">
                            </div>
                            <input id="editItemFile" type="file" accept="image/*"
                                @input="editForm.image = $event.target.files[0]" @change="updateEditImage" hidden>
                            <div class="w-96 m-2">
                                <div>
                                    <BreezeLabel for="name" value="Name" />
                                    <BreezeInput id="name" type="text" class="mt-1 block w-full" v-model="editForm.name"
                                        required autofocus />
                                </div>

                                <div class="mt-4">
                                    <BreezeLabel for="description" value="Description" />
                                    <BreezeInput id="description" type="text" class="mt-1 block w-full"
                                        v-model="editForm.description" />
                                </div>

                                <div class="mt-4">
                                    <BreezeLabel for="price" value="Price (₱)" />
                                    <BreezeInput id="price" type="number" class="mt-1 block w-full"
                                        v-model="editForm.price" />
                                </div>

                                <div class="mt-4">
                                    <BreezeLabel for="code" value="Code" />
                                    <BreezeInput id="code" type="text" class="mt-1 block w-full" v-model="editForm.code" />
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-2">
                            <button @click="showEditItemModal = false" type="button"
                                class="hover:underline dark:text-white/80">Cancel</button>
                            <button type="submit" :disabled="editForm.processing"
                                :class="{ 'opacity-25': editForm.processing }"
                                class="px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-900 text-sm rounded-lg">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
        <Transition enter-active-class="duration-200 ease opacity-0" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="duration-200 ease opacity-90" leave-from-class="opacity-90"
            leave-to-class="transform opacity-0" appear>
            <div v-if="showEditItemModal" class="fixed inset-0 z-40 bg-black/50 backdrop-blur-md"></div>
        </Transition>
    </div>

    <!-- Delete Item Modal -->
    <div>
        <Transition enter-active-class="duration-200 ease-out" enter-from-class="transform opacity-0 scale-75"
            enter-to-class="opacity-100 scale-100" leave-active-class="duration-200 ease-out"
            leave-from-class="opacity-100 scale-100" leave-to-class="transform opacity-0 scale-75">
            <div v-if="showDeleteItemModal" class="inset-0 fixed z-50 h-screen w-screen flex justify-center items-center"
                @click.self="showDeleteItemModal = false">
                <div
                    class="relative bg-white dark:bg-zinc-900 w-full lg:w-1/4 h-auto max-h-[80%] p-6 rounded-lg dark:text-white overflow-auto">
                    <span class="font-bold text-lg block mb-2">Confirmation</span>
                    <p>Are you sure you want to delete <span class="font-bold">{{ deleteItem.name }}</span>?</p>
                    <div class="flex justify-end space-x-4 mt-4">
                        <button @click="showDeleteItemModal = false" class="text-sm hover:underline">
                            Cancel
                        </button>
                        <button @click="Inertia.delete(route('items.destroy', deleteItem), {
                            preserveScroll: true,
                            onSuccess: () => {
                                showDeleteItemModal = false
                            }
                        })"
                            class="rounded-lg bg-red-600 hover:bg-red-700 active:bg-red-900 text-white text-xs px-4 py-2">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
        <Transition enter-active-class="duration-200 ease opacity-0" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="duration-200 ease opacity-90" leave-from-class="opacity-90"
            leave-to-class="transform opacity-0" appear>
            <div v-if="showDeleteItemModal" class="fixed inset-0 z-40 bg-black/50 backdrop-blur-md"></div>
        </Transition>
    </div>
</template>