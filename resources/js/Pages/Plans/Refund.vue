<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
  photo: {
    type: Object,
  },
  message: {
    type: String,
  }
});

const form = useForm({});

const handleRequest = () => {
  form.post(route('refund.process'), {
    onFinish: () => {
      console.log('finished');
    }
  })
}
</script>

<template>
  <AuthenticatedLayout>

    <Head :title="`| ${$t('plans.Refund')}`" />
    <template #header>
      <h2 class="font-semibold text-3xl text-gray-800 leading-tight text-center">{{ $t('plans.RefundRequest') }}</h2>
      <h2 class="font-semibold text-lg md:text-xl text-gray-600 leading-tight text-center">{{ $t('plans.RefundTitle1') }}</h2>
      <h2 class="font-semibold text-lg md:text-xl text-gray-600 leading-tight text-center">{{ $t('plans.RefundTitle2') }}</h2>
    </template>

    <div class="pb-12">
      <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 space-y-6">
        <div class="text-center">
          <button v-if="$page.props.auth.user.last_payment && $page.props.auth.user.last_payment != 'refunded'" class="bg-primary text-white px-4 py-3 rounded-md border-0 cursor-pointer"
            @click="handleRequest">{{ $t('plans.RefundRequest') }}</button>
          <button v-else-if="$page.props.auth.user.last_payment && $page.props.auth.user.last_payment == 'refunded'" class="bg-gray-400 text-white px-4 py-3 rounded-md border-0 cursor-pointer">{{ $t('plans.Refunded') }}</button>
          <button v-else class="bg-gray-400 text-white px-4 py-3 rounded-md border-0 cursor-pointer">{{ $t('plans.CantRefund') }}</button>
        </div>

        <h3 v-if="message"
          class="font-semibold text-lg md:text-xl text-primary leading-tight text-center">{{ message }}</h3>

        <h3 v-else-if="photo.downloaded && JSON.parse(photo.downloaded).length > 0"
          class="font-semibold text-lg md:text-xl text-red-600 leading-tight text-center">{{ $t('plans.RefundWarnning', {download: JSON.parse(photo.downloaded).length}) }}
      </h3>

    </div>
  </div>
</AuthenticatedLayout></template>
