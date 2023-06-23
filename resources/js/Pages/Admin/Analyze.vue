<script setup>
import { h, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { NTag } from 'naive-ui';

const props = defineProps({
  data: {
    type: Object
  },
  search: Object,
  csrfToken: String,
});

const columns = [
  {
    title: 'SignUps', key: 'total_signups', align: 'center'
  },
  { title: 'Sales', key: 'tphotos', align: 'center' },
  {
    title: '$25', key: 'tplan1', align: 'center'
  },
  {
    title: '$55', key: 'tplan2', align: 'center'
  },
  {
    title: '$155', key: 'tplan3', align: 'center'
  },
  {
    title: 'Total', key: 'tspent',
    render(row) {
      return h(NTag,
        {
          type: 'info',
          bordered: false,
        },
        { default: () => `$${row.tspent}` });
    }
  },
  { title: 'UTM Source', key: 'utm_source', align: 'center' },
  { title: 'UTM Medium', key: 'utm_medium', align: 'center' },
  { title: 'UTM Campaign', key: 'utm_campaign', align: 'center' },
  { title: 'UTM Content', key: 'utm_content', align: 'center' },
];

const form = useForm({
  from: props.search.from,
  to: props.search.to,
});

const dateRange = ref((props.search.from && props.search.to) ? [new Date(props.search.from + ' 00:00:00').getTime(), new Date(props.search.to + ' 00:00:00').getTime()] : null);
const current_page = ref(props.data.current_page);
const aData = ref(props.data.data.filter(da => da.total_signups > 0));

const rangeShortcuts = {
  'Today': [Date.now(), Date.now()],
  'Last 7 days': () => {
    const cur = Date.now();
    return [cur - 7 * 24 * 60 * 60 * 1000, cur]
  },
  'Last 15 days': () => {
    const cur = Date.now();
    return [cur - 15 * 24 * 60 * 60 * 1000, cur]
  },
  'Last 30 days': () => {
    const cur = Date.now();
    return [cur - 30 * 24 * 60 * 60 * 1000, cur]
  }
}

const pageChange = (page) => {
  current_page.value = page;
  let actLink = route('admin.analyze', { page });
  form.get(actLink, {
    onFinish: () => console.log('finished.')
  });
}

const disablePreviousDate = (ts) => {
  return ts > Date.now()
}

const handleChange = (value) => {
  if (value) {
    form.from = value[0];
    form.to = value[1];
  } else {
    form.from = undefined;
    form.to = undefined;
  }
  pageChange(1);
}

</script>

<template>
  <AuthenticatedLayout>

    <Head title="| Admin - Analyze" />
    <template #header>
      <div class="max-w-5xl mx-auto text-center">
        <h2 class="text-xl md:text-2xl font-bold tracking-widest text-black">
          Analyze
        </h2>
      </div>
    </template>
    <div class="px-3 lg:px-6">
      <div class="flex justify-end flex-wrap">
        <div class="mb-5">
          <n-date-picker size="large" :is-date-disabled="disablePreviousDate" v-model:value="dateRange" type="daterange"
            clearable :shortcuts="rangeShortcuts" @update:formatted-value="handleChange" />
        </div>
      </div>
      <n-data-table :columns="columns" :data="aData" />
      <div class="p-3">
        <n-pagination class="justify-end" :page-count="data.last_page" show-quick-jumper :page="data.current_page"
          @update:page="pageChange">
          <template #goto>
            <span class="text-white">Goto</span>
          </template>
        </n-pagination>
      </div>
    </div>
  </AuthenticatedLayout></template>
