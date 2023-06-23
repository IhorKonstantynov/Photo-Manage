<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ShoppingCartArrowDown, ShoppingCartPlus, ShoppingCartArrowUp, ShoppingCart } from '@vicons/carbon';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    plans: {
        type: Array,
    },
    is_beginner: {
        type: Boolean,
        default: false,
    }
});

const form = useForm({
    count: 5,
});
let showModalRef = ref(false);
let actPlanId = usePage().props.auth.user.plan_id;
let actPlan = actPlanId ? props.plans.filter(plan => actPlanId == plan.id)[0] : {};
let selectedPlan;
const selectPlan = (plan) => {
    if (!props.is_beginner) {
        selectedPlan = plan;
        showModalRef.value = true;
    } else {
        if(!(plan.account_type == 1 && plan.slug != "Beginner")) {
            form.transform(()=>{}).get(route('plans.show', plan.slug), {
                onFinish: () => console.log('finished')
            });
        } else {
            form.get(route('plans.show', plan.slug), {
                onFinish: () => console.log('finished'),
            });
        }
    }
};

const submitPlan = () => {
    if (!props.is_beginner) {
        form.transform((data) => ({
            ...data,
            plan: selectedPlan.id
        })).post(route('charge.create'), {
            onFinish: () => console.log('finished'),
        });
    }
}

const newCard = () => {
    showModalRef.value = false;
    if(!(selectedPlan.account_type == 1 && selectedPlan.slug != "Beginner")) {
        form.transform(()=>{}).get(route('plans.show', selectedPlan.slug), {
            onFinish: () => console.log('finished')
        });
    } else {
        form.get(route('plans.show', selectedPlan.slug), {
            onFinish: () => console.log('finished'),
        });
    }
}


</script>

<template>
    <AuthenticatedLayout>

        <Head :title="`| ${$t('plans.Plans')}`" />
        <template #header>
            <div class="max-w-2xl my-3 md:my-5 mx-auto text-center">
                <h2 class="text-xl font-bold md:text-2xl lg:text-3xl text-black whitespace-pre-wrap" v-html="$t('plans.Title', {photos: '1,224,649', users: '10,389'})">
                </h2>
            </div>
        </template>

        <div class="md:pb-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="flex flex-wrap items-stretch justify-center sm:-mx-4">
                    <div v-for="plan in plans" class="w-full mb-8 px-2 sm:px-4 md:w-1/2 lg:w-1/3 lg:mb-0" :key="plan.slug">
                        <div class="h-full flex border border-solid border-gray-300 flex-col p-6 space-y-6 rounded shadow sm:p-8 bg-white">
                            <div class="space-y-3">
                                <h4 class="text-2xl font-bold">{{ $t(`plans.${plan.name}`) }}</h4>
                                <span class="font-bold"
                                    :class="($page.props.auth.user.promo_code && plan.account_type == 0) ? 'line-through text-2xl text-gray-500 mr-3' : 'text-primary text-4xl'">${{
                                        plan.price }}
                                </span>
                                <span v-if="$page.props.auth.user.promo_code && plan.account_type == 0"
                                    class="text-4xl font-bold">${{
                                        plan.price * 0.8 }}</span>
                                <span class="text-xl text-gray-600" v-if="plan.account_type == 1 && plan.slug != 'Beginner'">/Employee x {{ form.count }} = ${{ plan.price * form.count }}</span>

                                <h5 class="text-md text-gray-500">
                                    {{ plan.description }}
                                </h5>
                            </div>

                            <ul class="flex-1 mb-6">
                                <li v-for="service in plan.services.split('_')" class="flex mb-2 space-x-2" :key="service + plan.slug">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                        class="flex-shrink-0 w-6 h-6">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span>{{ service }}</span>
                                </li>
                            </ul>
                            <div class="mt-4" v-if="plan.account_type == 1 && plan.slug != 'Beginner'">
                                <InputLabel for="count" value="Employees" />

                                <TextInput id="count" type="number" min="10" class="mt-1 block w-full" v-model="form.count"
                                    required />

                                <InputError class="mt-2" :message="form.errors.count" />
                            </div>
                            <button v-if="$page.props.auth.user.plan_id !== plan.id" type="button" @click="selectPlan(plan)"
                                class="border-0 cursor-pointer bg-primary w-full text-white inline-block px-5 py-3 font-semibold tracking-wider text-center rounded">
                                <span class="inline-flex items-center" v-if="!$page.props.auth.user.plan_id">
                                    <n-icon class="mr-2 text-xl">
                                        <ShoppingCartPlus />
                                    </n-icon>
                                    {{ $t('plans.Purchase') }}
                                </span>
                                <span class="inline-flex items-center" v-else-if="plan.price > actPlan.price">
                                    <n-icon class="mr-2 text-xl">
                                        <ShoppingCartArrowUp />
                                    </n-icon>
                                    {{ $t('plans.Upgrade') }}
                                </span>
                                <span class="inline-flex items-center" v-else>
                                    <n-icon class="mr-2 text-xl">
                                        <ShoppingCartArrowDown />
                                    </n-icon>
                                    {{ $t('plans.Downgrade') }}
                                </span>
                            </button>
                            <button v-else type="button"
                                class="border-0 bg-gray-400 w-full text-white inline-block px-5 py-3 font-semibold tracking-wider text-center rounded">
                                <span class="inline-flex items-center">
                                    <n-icon class="mr-2 text-xl">
                                        <ShoppingCart />
                                    </n-icon>
                                    {{ $t('plans.Purchased') }}
                                </span>
                            </button>
                            <div class="flex justify-center">
                                <img class="w-10 mr-1" src="/imgs/credit_cards/visa.png" alt="visa">
                                <img class="w-10 mr-1" src="/imgs/credit_cards/mastercard.png" alt="mastercard">
                                <img class="w-10 mr-1" src="/imgs/credit_cards/jcb.png" alt="jcb">
                                <img class="w-10" src="/imgs/credit_cards/american-express.png" alt="american-express">
                            </div>
                            <div class="text-center"><n-text class="italic text-gray-400">{{ $t('plans.Info') }}</n-text></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pb-12 max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
            <div class="flex justify-between items-center">
                <div class="border-b-2 border-0 border-solid border-gray-300 h-3 flex-[0.5]"></div>
                <h2 class="text-xl md:text-2xl mx-3 font-bold">{{ $t('plans.FeaturedOn') }}</h2>
                <div class="border-b-2 border-0 border-solid border-gray-300 h-3 flex-[0.5]"></div>
            </div>
            <div class="flex flex-wrap items-center justify-around">
                <img class="pt-5 md:w-1/5 w-1/2 px-12 md:px-6 lg:px-10" src="/imgs/nbc_news.png" alt="NBC NEWS" />
                <img class="pt-5 md:w-1/5 w-1/2 px-12 md:px-6 lg:px-10" src="/imgs/usa_today.png" alt="NBC NEWS" />
                <img class="pt-5 md:w-1/5 w-1/2 px-[6rem] md:px-12 lg:px-16 xl:px-[5rem]" src="/imgs/cbs.png"
                    alt="NBC NEWS" />
                <img class="pt-5 md:w-1/5 w-1/2 px-14 md:px-6 lg:px-12" src="/imgs/fox_new.png" alt="NBC NEWS" />
            </div>
        </div>
        <n-modal v-model:show="showModalRef" :mask-closable="false" preset="dialog" title="Confirm"
            content="Would you like to use the card from your previous purchase?">
            <template #action>
                <div class="flex">
                    <n-button class="text-white bg-primary mr-3" @click="submitPlan"
                        :loading="form.processing">{{ $t('common.Yes') }}</n-button>
                    <n-button @click="newCard">{{ $t('common.No') }}</n-button>
                </div>
            </template>
        </n-modal>
    </AuthenticatedLayout>
</template>
