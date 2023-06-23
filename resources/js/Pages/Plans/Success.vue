<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    message: {
        type: String,
    }
});
</script>

<template>
    <AuthenticatedLayout>

        <Head title="| Subscribe Success" />
        <template #header>
            <h2 class="font-semibold text-2xl text-primary leading-tight text-center">Charge Result.</h2>
        </template>
        
        <div class="pb-12">
            <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 space-y-6">
                <h3 class="font-semibold text-xl text-primary leading-tight text-center">{{ message }}</h3>
                <div class="text-center">
                    <Link class="bg-primary text-white px-4 py-3 rounded-md" :href="route('home')">Go Home</Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
