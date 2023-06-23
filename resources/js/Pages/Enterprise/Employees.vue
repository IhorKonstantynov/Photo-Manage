<script setup>
import { h, ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { NTag } from 'naive-ui';
import { toast } from 'vue3-toastify';
import moment from 'moment';
import { UserPlus } from '@vicons/fa';
import { trans } from 'laravel-vue-i18n';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
  employees: {
    type: Object
  },
  csrfToken: String
});

const columns = computed({
  get() {
    return [
      { title: trans('common.No'), key: 'id' },
      { title: trans('auth.Email'), key: 'email' },
      {
        title: trans('common.Status'), key: 'status',
        render(row) {
          return h(NTag,
            {
              class: "",
              type: 'info',
              bordered: false
            },
            { default: () => row.status == 0 ? trans('common.Pending') : trans('common.Accepted') });
        }
      },
      {
        title: trans('common.Generated'), key: 'generated',
        render(row) {
          if (row.generated > 0) {
            return h(Link,
              {
                class: "flex justify-center bg-gray-100 cursor-pointer hover:bg-gray-300 px-[10px] py-[5px] rounded",
                href: route('enterprise.employee.photos', row.id)
              },
              { default: () => `${row.generated}` });
          } else {
            return h(NTag,
              {
                class: "flex justify-center bg-gray-100 px-[10px] py-[5px] rounded",
                type: 'warning',
                bordered: false,
              },
              { default: () => trans(`common.No`) });
          }
        }
      },
      {
        title: trans('common.Date'), key: 'created_at',
        render(row) {
          return moment(row.created_at).format("YY-MM-DD HH:mm:ss");
        }
      },
    ]
  }
});

const form = useForm({
  emails: [""],
});
const exData = ref({
  _token: props.csrfToken
});
const showInviteModal = ref(false);
const employeeData = ref(props.employees.data ?? []);

const pageChange = (page) => {
  console.log(page);
  let actLink = props.employees.links.filter(link => link.label == page)[0];
  form.transform(() => ({})).get(actLink.url, {
    onFinish: () => console.log('finished.')
  });
}

const sortChange = (options) => {
  console.log(options)
}

const handleInvite = () => {
  showInviteModal.value = false;
  form.post(route('enterprise.invite') + window.location.search, {
    onFinish: () => {
      form.emails = [""];
      toast.success(trans('enterprise.InviteSuccess'));
      employeeData.value = props.employees.data ?? [];
    }
  });
}

</script>

<template>
  <AuthenticatedLayout>

    <Head :title="`| ${$t('enterprise.Employees')}`" />
    <div class="p-3 max-w-5xl m-auto">
      <div class="mt-3 mb-5 flex justify-end">
        <PrimaryButton @click="showInviteModal = true" class="flex items-center mt-6 px-10 py-2">
          <n-icon class="text-xl">
            <UserPlus />
          </n-icon>
          <span class="ml-2">{{ $t('enterprise.InvitePeople') }}</span>
        </PrimaryButton>
      </div>
      <n-data-table :columns="columns" :data="employeeData" @update:sorter="sortChange" />
      <div class="p-3">
        <n-pagination class="justify-end" :page-count="employees.last_page" show-quick-jumper
          :page="employees.current_page" @update:page="pageChange">
          <template #goto>
            <span class="text-white">{{ $t('pagination.Goto') }}</span>
          </template>
        </n-pagination>
      </div>
    </div>
    <n-modal v-model:show="showInviteModal" class="custom-card" preset="card" :style="{
      width: '400px'
    }" :title="`🙋‍♂️ ${$t('enterprise.InvitePeople')}`" :bordered="false" size="small" :segmented="{
  content: 'soft',
  footer: 'soft'
}">
      <div class="mt-4">
        <n-dynamic-input v-model:value="form.emails" :placeholder="$t('auth.Email')" :min="1"
          :max="$page.props.auth.user.available_employee" :disabled="!$page.props.auth.user.available_employee" />
      </div>
      <n-alert class="mt-4" type="warning" v-if="!$page.props.auth.user.available_employee">
        {{ $t('enterprise.InviteWarning') }}
        <Link :href="route('profile.plans')"
          class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        {{ $t('common.here') }}.
        </Link>
      </n-alert>
      <template #footer>
        <div class="flex justify-end">
          <PrimaryButton @click="showInviteModal = false" class="flex items-center px-4 py-2 mr-3 bg-red-600">
            <span>{{ $t('common.Cancel') }}</span>
          </PrimaryButton>
          <PrimaryButton @click="handleInvite" :disabled="!$page.props.auth.user.available_employee"
            class="flex items-center px-4 py-2">
            <span>{{ $t('common.Invite') }}</span>
          </PrimaryButton>
        </div>
      </template>
    </n-modal>
  </AuthenticatedLayout>
</template>
