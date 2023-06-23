<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import swal from 'sweetalert';
import { HandHoldingUsd, UserCheck } from '@vicons/fa';
import { trans } from 'laravel-vue-i18n';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
  totalCount: Number,
  totalSale: Number,
  totalPay: Number,
  pendingPay: Number,
  earningPay: Number,
  currentPay: Number,
  refundedPay: Number,
  user: Object,
});

const form = useForm({});

const exData = ref({
  _token: props.csrfToken
});

const handleRequest = () => {
  form.post(route('referrals.request'), {
    onFinish: () => {
      swal(`${trans('common.Success')}!`, trans('refferals.PaymentRequestSuccess'), "success");
    }
  })
}

const handleConfirm = () => {
  swal({
    title: trans('refferals.ConfirmQuiz'),
    text: trans('ConfirmPayQuiz', {name: props.user.name, price: props.pendingPay}),
    icon: "info",
    buttons: true,
    dangerMode: true,
  }).then((isConfirm) => {
    if (isConfirm) {
      form.post(route('admin.referrals.request.confirm', props.user.id), {
        onFinish: () => {
          swal(`${trans('common.Success')}!`, trans('refferals.ConfirmSuccess'), "success");
        }
      })
    }
  });
}

</script>

<template>
  <Head title="| Referrals" />
  <AuthenticatedLayout>
    <template #header v-if="user">
      <div class="max-w-6xl mx-auto text-center bg-white pb-10 pt-5 px-2 sm:px-6 lg:px-8">
        <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-600 leading-5 md:leading-4">
          {{ $t('refferals.RefferalsTitle', { name: user.name }) }}
        </h2>
      </div>
    </template>
    <div>
      <div class="max-w-6xl mx-auto pb-10 px-2 sm:px-6 lg:px-8 mt-10">
        <div class="flex flex-wrap justify-around">
          <div class="w-1/5 rounded text-center py-5 bg-red-200 shadow">
            <h2 class="text-sm sm:text-md lg:text-lg font-bold text-gray-700">
              {{ $t('refferals.SignUps') }}
            </h2>
            <p class="text-3xl font-bold text-black">
              {{ totalCount }}
            </p>
          </div>
          <div class="w-1/5 rounded text-center py-5 bg-green-200 shadow">
            <h2 class="text-sm sm:text-md lg:text-lg font-bold text-gray-700">
              {{ $t('refferals.Sales') }}
            </h2>
            <p class="text-3xl font-bold text-black">
              {{ totalSale }}
            </p>
          </div>
          <div class="w-1/5 rounded text-center py-5 bg-yellow-200 shadow">
            <h2 class="text-sm sm:text-md lg:text-lg font-bold text-gray-700">
              {{ $t('refferals.TotalSales') }}
            </h2>
            <p class="text-3xl font-bold text-black">
              ${{ totalPay }}
            </p>
          </div>
          <div class="w-1/5 rounded text-center py-5 bg-blue-200 shadow">
            <h2 class="text-sm sm:text-md lg:text-lg font-bold text-gray-700">
              {{ $t('refferals.UserRefunded') }}
            </h2>
            <p class="text-3xl font-bold text-black">
              ${{ refundedPay }}
            </p>
          </div>
          <div class="w-full flex justify-around mt-5">
            <div class="w-1/5 rounded text-center py-5 bg-blue-200 shadow">
              <h2 class="text-sm sm:text-md lg:text-lg font-bold text-gray-700">
                {{ $t('refferals.Commission') }}
              </h2>
              <p class="text-3xl font-bold text-black">
                ${{ currentPay }}
              </p>
            </div>
            <div class="w-1/5 rounded text-center py-5 bg-purple-200 shadow">
              <h2 class="text-sm sm:text-md lg:text-lg font-bold text-gray-700">
                {{ $t('refferals.Requested') }}
              </h2>
              <p class="text-3xl font-bold text-black">
                ${{ pendingPay }}
              </p>
            </div>
            <div class="w-1/5 rounded text-center py-5 bg-red-200 shadow">
              <h2 class="text-sm sm:text-md lg:text-lg font-bold text-gray-700">
                {{ $t('refferals.Paid') }}
              </h2>
              <p class="text-3xl font-bold text-black">
                ${{ earningPay }}
              </p>
            </div>
          </div>
        </div>
        <div class="text-center mt-10">
          <PrimaryButton v-if="$page.props.auth.user.permission == 2 || !route().current('admin.review.request')"
            class="capitalize flex items-center px-[30px] py-3 hover:shadow-lg" @click="handleRequest"
            :disabled="currentPay == 0">
            <n-icon class="text-xl mr-2">
              <HandHoldingUsd />
            </n-icon>
            <span class="text-[1rem]">{{ $t('refferals.RequestPayment') }}</span>
          </PrimaryButton>
          <PrimaryButton v-else class="capitalize flex items-center px-[30px] py-3 hover:shadow-lg" @click="handleConfirm"
            :disabled="pendingPay == 0">
            <n-icon class="text-xl mr-2">
              <UserCheck />
            </n-icon>
            <span class="text-[1rem]">{{ $t('refferals.ConfirmPayment') }}</span>
          </PrimaryButton>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
