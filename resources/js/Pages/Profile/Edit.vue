<script setup>
import { watch } from "vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import SetPromoCodeForm from './Partials/SetPromoCodeForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { toast } from 'vue3-toastify';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

watch(() => props.status, () => {
    if (props.status == 'promo-updated') {
        toast.success($t('PromoEmailSuccess'), {
            atoClose: 3000,
        });
    }
});

</script>

<template>
    <AuthenticatedLayout>

        <Head :title="$t('Profile')" />
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">{{ $t('Profile') }}</h2>
        </template>

        <div class="pb-12">
            <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow border sm:rounded-lg">
                    <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" class="max-w-xl" />
                </div>

                <div class="p-4 sm:p-8 bg-white shadow border sm:rounded-lg">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div v-if="$page.props.auth.user.account_type == 0" class="p-4 sm:p-8 bg-white shadow border sm:rounded-lg">
                    <SetPromoCodeForm class="max-w-xl" />
                </div>

                <div class="p-4 sm:p-8 bg-white shadow border sm:rounded-lg">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
