<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';
import { trans } from 'laravel-vue-i18n';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
  enterprise: {
    type: Object
  }
});

const form = useForm({
  token: props.enterprise.token,
});

const handleAccept = () => {
  // showInviteModal.value = false;
  form.post(route('user.enterprises.accept', props.enterprise.token), {
    onFinish: () => {
      toast.success(trans('enterprise.AcceptSuccess'));
    }
  });
}

</script>

<template>
  <AuthenticatedLayout>

    <Head :title="`| ${$t('common.Accept')}`" />

    <template #header>
      <div class="max-w-5xl mt-10 mx-auto text-center">
        <h2 v-if="enterprise" class="text-xl md:text-2xl font-bold tracking-widest lg:text-4xl">
          {{ $t('enterprise.AcceptTitle', {name: enterprise.enterprise.name}) }}
        </h2>
      </div>
    </template>

    <div class="p-3 max-w-5xl m-auto">
      <div class="flex justify-center">
        <PrimaryButton v-if="enterprise" @click="handleAccept" class="flex items-center mt-6 px-10 py-3">
          <span>🤝 {{ $t('common.Accept') }}</span>
        </PrimaryButton>
        <PrimaryButton v-else class="flex items-center bg-gray-400 mt-6 px-10 py-3">
          <span>{{ $t('enterprise.InvalidLink') }}</span>
        </PrimaryButton>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
