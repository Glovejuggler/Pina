<script setup>
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
import { useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
    auth: Object,
    success: Object,
    errors: Object
})

const accountForm = useForm({
    email: props.auth.user.email,
    name: props.auth.user.name
})

const passwordForm = useForm({
    currentPassword: '',
    newPassword: '',
    confirmPassword: ''
})
</script>

<template>
    <div class="mx-auto max-w-screen-2xl py-6 px-8">
        <span class="font-bold text-lg text-gray-800">Account Settings</span>
        <div class="bg-white rounded-lg border p-6 w-1/3 mb-4">
            <form @submit.prevent="accountForm.put(route('user.update', auth.user), {
                preserveScroll: true,
                preserveState: true,
            })">
                <div>
                    <BreezeLabel for="email" value="Email" />
                    <BreezeInput id="email" name="email" type="text" v-model="accountForm.email" class="w-full" required />
                    <span v-if="errors.email" class="text-sm text-red-500">{{ errors.email }}</span>
                </div>

                <div class="mt-4">
                    <BreezeLabel for="name" value="Name" />
                    <BreezeInput id="name" name="name" type="text" v-model="accountForm.name" class="w-full" required />
                    <span v-if="errors.name" class="text-sm text-red-500">{{ errors.name }}</span>
                </div>

                <div class="mt-4">
                    <button class="px-4 py-2 rounded-lg bg-gray-800 text-white text-sm" v-wave>Update</button>
                </div>
            </form>
        </div>

        <span class="font-bold text-lg text-gray-800">Change Password</span>
        <div class="bg-white rounded-lg border p-6 w-1/3 mb-4">
            <form @submit.prevent="passwordForm.put(route('password.change', auth.user), {
                preserveScroll: true,
                preserveState: true,
            })">
                <div>
                    <BreezeLabel for="currentPassword" value="Current password" />
                    <BreezeInput id="currentPassword" name="currentPassword" type="password"
                        v-model="passwordForm.currentPassword" class="w-full" required />
                    <span class="text-sm text-red-500" v-if="errors.currentPassword">{{ errors.currentPassword }}</span>
                </div>

                <div class="mt-4">
                    <BreezeLabel for="newPassword" value="New password" />
                    <BreezeInput id="newPassword" name="newPassword" type="password" v-model="passwordForm.newPassword"
                        class="w-full" required />
                    <span class="text-sm text-red-500" v-if="errors.newPassword">{{ errors.newPassword }}</span>
                </div>

                <div class="mt-4">
                    <BreezeLabel for="confirmPassword" value="Confirm new password" />
                    <BreezeInput id="confirmPassword" name="confirmPassword" type="password"
                        v-model="passwordForm.confirmPassword" class="w-full" required />
                    <span class="text-sm text-red-500" v-if="errors.confirmPassword">{{ errors.confirmPassword }}</span>
                </div>

                <div class="mt-4">
                    <button class="px-4 py-2 rounded-lg bg-gray-800 text-white text-sm" v-wave>Update</button>
                </div>
            </form>
        </div>

        <span class="font-bold text-lg text-gray-800">Application Settings</span>
        <div class="bg-white rounded-lg border p-6 w-1/3">
            <div class="flex justify-between">
                <span>Storage link</span>
                <button @click="$inertia.get(route('storage.link'))"
                    class="rounded-lg px-4 text-sm text-white bg-gray-800 leading-none tracking-wide" v-wave>
                    Link
                </button>
            </div>
            <div class="flex justify-between mt-4" v-if="$page.props.auth.user.name === 'Jonah'">
                <span>Reset data</span>
                <button @click="$inertia.get(route('migrate.fresh'))"
                    class="rounded-lg px-4 text-sm text-white bg-gray-800 leading-none tracking-wide" v-wave>Reset</button>
            </div>
        </div>

        {{ success }}
    </div>
</template>