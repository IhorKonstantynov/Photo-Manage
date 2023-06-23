<script setup>
import { h, ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { NTag } from 'naive-ui';
import moment from 'moment';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
  enterprises: {
    type: Object
  },
  csrfToken: String
});

const columns = computed({
  get() {
    return [
      { title: trans('auth.CompanyName'), key: 'enterprise.company_name' },
      { title: trans('auth.Name'), key: 'enterprise.name' },
      {
        title: 'Status', key: 'status',
        render(row) {
          if (row.status == 0) {
            return h(Link,
              {
                class: "flex justify-center bg-gray-100 cursor-pointer hover:bg-gray-300 px-[10px] py-[5px] rounded",
                href: route('user.enterprises.invite', row.token)
              },
              { default: () => trans('common.Accept') });
          } else {
            return h(NTag,
              {
                class: "flex justify-center bg-gray-100 px-[10px] py-[5px] rounded",
                type: 'success',
                bordered: false,
              },
              { default: () => trans('common.Accepted') });
          }
        }
      },
      {
        title: trans('common.Generated'), key: 'generated',
        render(row) {
          if (row.generated > 0) {
            return h(Link,
              {
                class: "flex justify-center bg-gray-100 cursor-pointer hover:bg-gray-300 px-[10px] py-[5px] rounded",
                href: 'javascript:void(0);'
              },
              { default: () => row.generated });
          } else {
            return h(NTag,
              {
                class: "flex justify-center bg-gray-100 px-[10px] py-[5px] rounded",
                type: 'warning',
                bordered: false,
              },
              { default: () => 0 });
          }
        }
      },
      {
        title: trans('common.Date'), key: 'created_at',
        render(row) {
          return moment(row.created_at).format("YY-MM-DD HH:mm:ss");
        }
      },
      {
        title: '#', key: 'id',
        render(row) {
          if (row.status == 1) {
            return h(Link,
              {
                class: "flex justify-center bg-white text-primary cursor-pointer hover:bg-gray-300 px-[10px] py-[5px] rounded",
                href: route('user.enterprises.upload', row.id)
              },
              { default: () => trans(`📸 ${trans('common.Upload')}`) });
          }
        }
      },
    ]
  }
})

const form = useForm({
  emails: [""],
});
const exData = ref({
  _token: props.csrfToken
});
const showInviteModal = ref(false);
const enterprisesData = ref(props.enterprises.data ?? []);

const pageChange = (page) => {
  console.log(page);
  let actLink = props.enterprises.links.filter(link => link.label == page)[0];
  form.transform(() => ({})).get(actLink.url, {
    onFinish: () => console.log('finished.')
  });
}

const sortChange = (options) => {
  console.log(options)
}

const handleAccept = () => {
  // showInviteModal.value = false;
  // form.post(route('enterprise.invite') + window.location.search, {
  //   onFinish: () => {
  //     form.emails = [""];
  //     toast.success("Successfully invited.");
  //     enterprisesData.value = props.enterprises.data ?? [];
  //   }
  // });
}

</script>

<template>
  <AuthenticatedLayout>

    <Head :title="`| ${$t('enterprise.Enterprises')}`" />

    <template #header>
      <div class="max-w-5xl mt-10 mx-auto text-center">
        <h2 class="text-xl md:text-2xl font-bold tracking-widest lg:text-4xl">
          {{ $t('enterprise.Enterprises') }}
        </h2>
      </div>
    </template>

    <div class="p-3 max-w-5xl m-auto">
      <n-data-table :columns="columns" :data="enterprisesData" @update:sorter="sortChange" />
      <div class="p-3">
        <n-pagination class="justify-end" :page-count="enterprises.last_page" show-quick-jumper
          :page="enterprises.current_page" @update:page="pageChange">
          <template #goto>
            <span class="text-white">{{ $t('pagination.Goto') }}</span>
          </template>
        </n-pagination>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
