<script setup>
import { h, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import moment from 'moment';
import { NTag } from 'naive-ui';

import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js';
import { Line } from 'vue-chartjs';

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
);

const props = defineProps({
  data: {
    type: Array
  },
  search: Object,
  csrfToken: String,
});

const cdata = {
  labels: props.data.map((d) => moment(d.created_at).format('YY-MM-DD')),
  datasets: [
    {
      label: 'Sign Ups',
      backgroundColor: '#f87979',
      data: props.data.map((d) => d.total_signups)
    },
    {
      label: 'Total Sales',
      backgroundColor: '#560',
      data: props.data.map((d) => d.tphotos)
    }
  ],
}

const options = {
  responsive: true,
  maintainAspectRatio: false
}


const columns = [
  {
    title: 'SignUps', key: 'total_signups', align: 'center'
  },
  { title: 'Sales', key: 'photos_count', align: 'center' },
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
  from: undefined,
  to: undefined,
});

const dateRange = ref((props.search.from && props.search.to) ? [new Date(props.search.from + ' 00:00:00').getTime(), new Date(props.search.to + ' 00:00:00').getTime()] : [Date.now() - 30 * 24 * 60 * 60 * 1000, Date.now()]);

const rangeShortcuts = {
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

const pageChange = () => {
  let actLink = route('admin.dashboard');
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
  pageChange();
}

</script>

<template>
  <AuthenticatedLayout>

    <Head title="| Admin - Dashboard" />
    <template #header>
      <div class="max-w-5xl mx-auto text-center">
        <h2 class="text-xl md:text-2xl font-bold tracking-widest text-black">
          Dashbaord
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
      <!-- <n-data-table :columns="columns" :data="aData" />
      <div class="p-3">
        <n-pagination class="justify-end" :page-count="data.last_page" show-quick-jumper :page="data.current_page"
          @update:page="pageChange">
          <template #goto>
            <span class="text-white">Goto</span>
          </template>
        </n-pagination>
      </div> -->
      <div class="p-3">
        <Line :data="cdata" :options="options" height="400"/>
      </div>
    </div>
  </AuthenticatedLayout></template>
