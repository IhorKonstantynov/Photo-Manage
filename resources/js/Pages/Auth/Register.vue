<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';

import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const search = window.location.search;

const accTypes = computed({
    get() {
        return [
            {
                value: 0,
                label: trans('auth.Personal')
            },
            {
                value: 1,
                label: trans('auth.Enterprise')
            },
        ]
    }
})

const form = useForm({
    name: '',
    email: '',
    password: '',
    account_type: 0,
    company_name: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        company_name: data.account_type == 0 ? null : data.company_name
    })).post(route('register') + search, {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head :title="$t('auth.Register')" />

        <form @submit.prevent="submit" class="w-full">
            <h2 class="text-xl py-3 font-bold lg:text-2xl text-primary text-center uppercase">{{ $t('auth.Register') }}
            </h2>
            <div>
                <InputLabel for="name" :value="$t('auth.Name')" />
                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus
                    autocomplete="name" />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div class="mt-4">
                <InputLabel for="email" :value="$t('auth.Email')" />

                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                    autocomplete="username" />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" :value="$t('auth.Password')" />

                <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required
                    autocomplete="new-password" />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" :value="$t('auth.ConfirmPassword')" />

                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                    v-model="form.password_confirmation" required autocomplete="new-password" />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-4">
                <InputLabel for="account_type" :value="$t('auth.AccountType')" />

                <n-radio-group class="mt-1 block p-2 border-solid border-gray-400 border rounded-md shadow-sm"
                    id="account_type" v-model:value="form.account_type" name="radiogroup">
                    <n-space>
                        <n-radio v-for="accType in accTypes" :key="accType.value" :value="accType.value"
                            :label="accType.label" />
                    </n-space>
                </n-radio-group>
            </div>

            <div class="mt-4" v-if="form.account_type == 1">
                <InputLabel for="company_name" :value="$t('auth.CompanyName')" />

                <TextInput id="company_name" type="text" class="mt-1 block w-full" v-model="form.company_name" required
                    autocomplete="company_name" />

                <InputError class="mt-2" :message="form.errors.company_name" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login') + search"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ $t('auth.already') }}
                </Link>

                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ $t('auth.Register') }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
