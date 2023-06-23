<script setup>
import { h, ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { NTag, NButton } from 'naive-ui';
import moment from 'moment';
import swal from 'sweetalert';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import RadioButtonGroup from '@/Components/RadioButtonGroup.vue';
import { createApp } from 'vue';

const props = defineProps({
    users: {
        type: Object
    },
    plans: Array,
    csrfToken: String,
    search: Object,
});

// console.log(props.users, '<<<>>>>>>>>>>>>>>');

const accTypes = [
    {
        value: '2',
        label: "Both"
    },
    {
        value: '0',
        label: "Personal"
    },
    {
        value: '1',
        label: "Enterprise"
    },
]
const columns = [
    { title: 'ID', key: 'id' },
    {
        title: 'Name', key: 'name',
        // sortOrder: false,
        // sorter: "default"
    },
    { title: 'Email', key: 'email' },
    {
        title: 'Type', key: 'account_type',
        render(row) {
            if (row.account_type == 0) {

                return h(NTag,
                    {
                        type: 'success',
                        bordered: false,
                    },
                    { default: () => `Personal` + (row.enterprise.length > 0 ? '-Invited' : '') });
            }
            return h(NTag,
                {
                    type: 'warning',
                    bordered: false,
                },
                { default: () => `Enterprise` });
        }
    },
    {
        title: '🛒', key: 'plan',
        render(row) {
            if (row.account_type == 0) {
                return h(NTag,
                    {
                        type: 'info',
                        bordered: false
                    },
                    { default: () => row.plan ? (`$${row.plan.price}` + (row.promo_code ? `=> ${row.plan.price * 0.8}` : '')) : '$0' });
            }
            return h(NTag,
                {
                    type: 'success',
                    bordered: false
                },
                { default: () => (`👨‍👩‍👧‍👦 ${row.available_employee}, ` + (row.plan ? (`$${row.plan.price}`) : '$0')) });
        }
    },
    {
        title: 'Promo', key: 'promo_code'
    },
    {
        title: 'Spent', key: 'spent',
        render(row) {
            return h(NTag,
                {
                    style: { marginRight: '6px' },
                    type: 'success',
                    bordered: false,
                },
                { default: () => `$${row.spent}` });
        }
    },
    {
        title: '💲', key: 'credit'
    },
    {
        title: 'Generate', key: 'count',
        render(row) {
            if (row.count > 0) {
                return h(Link,
                    {
                        class: "flex justify-center bg-gray-100 cursor-pointer hover:bg-gray-300 px-[10px] py-[5px] rounded",
                        href: route('admin.user.photos', row.id)
                    },
                    { default: () => `${row.count}` });
            } else {
                return h(NTag,
                    {
                        class: "flex justify-center bg-gray-100 px-[10px] py-[5px] rounded",
                        type: 'warning',
                        bordered: false,
                    },
                    { default: () => `0` });
            }
        }
    },
    {
        title: 'BG', key: 'photo_edit',
        render(row) {
            if (!row.photo_edit) {
                return;
            }
            let gen = row.photo_edit.filter(ph => ph.bg_removed_img).length;
            return h(NTag,
                {
                    class: "flex justify-center bg-gray-100 px-[10px] py-[5px] rounded",
                    type: 'warning',
                    bordered: false,
                },
                { default: () => `${row.click_count}:${gen}` });
        }
    },
    {
        title: '✅', key: 'id',
        render(row) {
            if (row.photos.length == 0) return;
            return h(NTag,
                {
                    type: 'info',
                    bordered: false,
                },
                { default: () => row.photos[0].retouch == 1 ? 'Y' : 'N' });
        }
    },
    {
        title: '🔽', key: 'photos',
        render(row) {
            let downloaded = 0;
            if (row.photos.length > 0) {
                downloaded = row.photos.reduce(
                    (accumulator, currentValue) => {
                        if (currentValue.downloaded) {
                            return accumulator + JSON.parse(currentValue.downloaded).length;
                        }
                        return accumulator;
                    },
                    0
                );
            }
            if (downloaded > 0) {
                return h(Link,
                    {
                        class: "flex justify-center bg-gray-100 cursor-pointer hover:bg-gray-300 px-[10px] py-[5px] rounded",
                        href: route('admin.user.photos.downloads', row.id)
                    },
                    { default: () => `${downloaded}` });
            }
            return h(NTag,
                {
                    class: "flex justify-center bg-gray-100 px-[10px] py-[5px] rounded",
                    type: 'success',
                    bordered: false,
                },
                { default: () => `0` });
        }
    },
    {
        title: 'Ref💲', key: 'id',
        render(row) {
            return h(Link,
                {
                    class: (row.pendingPay == 0 ? 'bg-purple-200' : 'bg-blue-500 text-white hover:bg-blue-700') + " px-2 py-1 rounded",
                    bordered: false,
                    href: route('admin.referrals.request.confirm', row.id)
                },
                { default: () => `${row.totalPay}` });
        }
    },
    { title: 'UTM Source', key: 'utm_source' },
    { title: 'UTM Medium', key: 'utm_medium' },
    { title: 'UTM Campaign', key: 'utm_campaign' },
    { title: 'UTM Content', key: 'utm_content' },
    {
        title: 'Date', key: 'created_at',
        render(row) {
            return moment(row.created_at).format("MM/DD/YY hh:mm A");
        }
    },
    {
        title: '#', key: 'id',
        render(row) {
            if (!row.plan && row.account_type == 0) {
                return [h(NButton,
                    {
                        type: 'primary',
                        class: 'mr-1 mb-1',
                        strong: true,
                        title: "Give Free Credit.",
                        size: 'small',
                        onClick: () => giveCredit(row)
                    },
                    { default: () => 'Give 💲' }), h(NButton,
                        {
                            type: row.last_payment == 'refunded' ? 'error' : 'info',
                            strong: true,
                            size: 'small',
                            title: "Refund this user's last payment.",
                            disabled: (!row.last_payment || row.last_payment == 'refunded'),
                            onClick: () => userRefund(row)
                        },
                        { default: () => row.last_payment == 'refunded' ? 'Refunded' : 'Refund' })];
            } else if (!row.plan && row.account_type == 1) {
                return h(NButton,
                    {
                        type: 'primary',
                        class: 'mr-1 mb-1',
                        strong: true,
                        title: "Give Free Credit.",
                        size: 'small',
                        onClick: () => giveCredit(row)
                    },
                    { default: () => 'Give 💲' });
            }
        }
    },
];

const form = useForm({
    id: props.search.id,
    email: props.search.email,
    name: props.search.name,
    account_type: props.search.account_type,
    utm_source: props.search.utm_source,
    utm_campaign: props.search.utm_campaign,
});

const exData = ref({
    _token: props.csrfToken
});
const current_page = ref(props.users.current_page);
const userData = ref(props.users.data ?? []);

watch(() => form.id,
    () => {
        pageChange(1);
    });
watch(() => form.email,
    () => {
        pageChange(1);
    });
watch(() => form.name,
    () => {
        pageChange(1);
    });
watch(() => form.account_type,
    () => {
        pageChange(1);
    });
watch(() => form.utm_source,
    () => {
        pageChange(1);
    });
watch(() => form.utm_campaign,
    () => {
        pageChange(1);
    });

const pageChange = (page) => {
    current_page.value = page;
    let actLink = route('admin.users', { page });
    form.get(actLink, {
        onFinish: () => console.log('finished.')
    });
}

const sortChange = (options) => {
    console.log(options)
}

const giveCredit = (row) => {
    let pl = window.localStorage.getItem('plan');
    if (pl == 4) {
        window.localStorage.setItem('plan', '1');
    }
    let options = [
        {
            value: '1',
            label: "Basic($25)"
        },
        {
            value: '2',
            label: "Premium($55)"
        },
        {
            value: '3',
            label: "Professional($155)"
        }
    ];
    if (row.account_type == 1) {
        window.localStorage.setItem('plan', '4');
        options = [
            {
                value: '4',
                label: "Single($25)"
            }
        ];
    }
    const el = document.createElement('div');
    createApp({
        render: () => h(RadioButtonGroup, {
            options,
            value: 'plan'
        })
    }).mount(el);

    swal({
        text: "Please select plan:",
        content: el,
        buttons: ['Cancel', 'Give'],
    }).then((isConfirm) => {
        if (isConfirm) {
            axios.put(route('admin.users.credit'), {
                ...exData.value,
                id: row.id,
                plan_id: window.localStorage.getItem('plan'),
            }).then(() => {
                row.plan = props.plans[Number(window.localStorage.getItem('plan')) - 1];
                swal("Sent successfuly!", `${row.name} received free credit.`, "success");
            });
        }
    });
};

const userRefund = (row) => {
    if (!row.last_payment || row.last_payment == 'refunded') return;

    swal({
        title: "Are you sure?",
        text: "Are you sure want to refund " + row.name + "'s last payment",
        icon: "info",
        buttons: true,
        dangerMode: true,
    }).then((isConfirm) => {
        if (isConfirm) {
            axios.post(route('admin.refund', row.id), {
                ...exData.value,
            }).then(() => {
                row.last_payment = 'refunded';
                swal("Refunded successfuly!", `${row.name} will receive success mail.`, "success");
            });
        }
    });
};

</script>

<template>
    <AuthenticatedLayout>

        <Head title="| Admin - Users" />
        <template #header>
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-xl md:text-2xl font-bold tracking-widest text-black">
                    Users
                </h2>
            </div>
        </template>
        <div class="p-3">
            <div class="flex justify-around flex-wrap">
                <div class="my-2 max-w-[100px]">
                    <InputLabel for="userId" value="User ID" />
                    <TextInput placeholder="Search by..." id="suerId" type="search" class="mt-1 block w-full"
                        v-model="form.id" />
                </div>
                <div class="my-2">
                    <InputLabel for="email" value="Email" />
                    <TextInput placeholder="Search by..." id="email" type="search" class="mt-1 block w-full"
                        v-model="form.email" />
                </div>
                <div class="my-2">
                    <InputLabel for="name" value="Name" />
                    <TextInput placeholder="Search by..." id="name" type="search" class="mt-1 block w-full"
                        v-model="form.name" />
                </div>
                <div class="my-2">
                    <InputLabel for="account_type" value="Account Type" />
                    <n-radio-group class="mt-1 block p-2 border-solid border-gray-400 border rounded-md shadow-sm"
                        id="account_type" v-model:value="form.account_type" name="radiogroup">
                        <n-space>
                            <n-radio v-for="accType in accTypes" :key="accType.value" :value="accType.value"
                                :label="accType.label" />
                        </n-space>
                    </n-radio-group>
                </div>
                <div class="my-2">
                    <InputLabel for="utm_source" value="UTM Source" />
                    <TextInput placeholder="Search by..." id="utm_source" type="search" class="mt-1 block w-full"
                        v-model="form.utm_source" />
                </div>
                <div class="my-2">
                    <InputLabel for="utm_campaign" value="UTM Campaign" />
                    <TextInput placeholder="Search by..." id="utm_campaign" type="search" class="mt-1 block w-full"
                        v-model="form.utm_campaign" />
                </div>
            </div>
            <n-data-table :columns="columns" :data="userData" @update:sorter="sortChange" />
            <div class="p-3">
                <n-pagination class="justify-end" :page-count="users.last_page" show-quick-jumper :page="users.current_page"
                    @update:page="pageChange">
                    <template #goto>
                        <span class="text-white">Goto</span>
                    </template>
                </n-pagination>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
