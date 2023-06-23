<script setup>
import { ref, onMounted } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
  intent: {
    type: Object
  },
  stripe_pk: {
    type: String
  },
  isPayment: {
    type: Boolean,
    default: false
  },
  message: String,
});

const data = [
  {
    label: '1 credit ($2.99)',
    value: 0
  },
  {
    label: '6 credit ($9.99)',
    value: 1
  },
];

const form = useForm({
  count: data[0].value,
  token: ''
});

const disabled = ref(false);
const card = ref(null);
const prevCard = ref(props.isPayment);
const stripe = window.Stripe(props.stripe_pk)
const elements = stripe.elements()

const style = {
  base: {
    color: '#32325d',
    colorPrimary: '#6366f1',
    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
    fontSmoothing: 'antialiased',
    fontSize: '16px',
    '::placeholder': {
      color: '#aab7c4'
    }
  },
  invalid: {
    color: '#fa755a',
    iconColor: '#fa755a'
  }
}

const el = elements.create('card', { style: style })

function displayError(event) {
  const displayError = document.getElementById('card-errors')
  if (event.error) {
    displayError.innerText = event.error.message
  } else {
    displayError.innerText = ''
  }
}

onMounted(() => {
  el.mount(card.value)

  el.on('change', (event) => {
    displayError(event)
  })
})

const Submit = async () => {
  disabled.value = true
  if (prevCard.value) {
    form.transform((data) => ({
      ...data,
      token: null
    })).post(route('charge.payCredit'), {
      onFinish: () => {
        disabled.value = false;
        document.getElementById('card-errors').innerHTML = props.message;
      },
    });
  } else {
    const clientSecret = props.intent.client_secret;
    const result = await stripe.confirmCardSetup(clientSecret, {
      payment_method: {
        type: 'card',
        card: el,
        billing_details: {
          name: usePage().props.auth.user.name
        }
      }
    })
    if (result.error) {
      disabled.value = false
    } else {
      // Successful subscription payment
      // The subscription automatically becomes active upon payment.
      console.log('stripe submit success.');
      form.transform((data) => ({
        ...data,
        token: result.setupIntent.payment_method
      })).post(route('charge.payCredit'), {
        onFinish: () => {
          disabled.value = false;
        },
      });
    }
  }
}

const handleChange = (value) => {
  prevCard.value = value
}
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <div class="max-w-2xl mx-auto text-center">
        <h2 class="text-xl font-bold lg:text-2xl text-primary"> {{ $t('plans.CreditTitle') }}</h2>
        <n-alert v-if="message" title="Card Error!" type="error" closable>
          {{ message }}
        </n-alert>
      </div>
    </template>

    <div class="pb-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="flex flex-wrap items-stretch justify-center sm:-mx-4">
          <div class="mb-8 px-2 sm:px-4 w-4/5 md:w-3/5 lg:w-2/5 xl:w-1/3 lg:mb-0">
            <div class="h-full flex flex-col p-6 space-y-6 rounded shadow sm:p-8 bg-white border border-solid border-gray-300">
              <div class="space-y-3">
                <div>
                  <n-select v-model:value="form.count" :options="data" size="large" :placeholder="$t('plans.CreditAmount')" />
                </div>
                <!-- <h4 class="text-2xl font-bold">{{ plan.name }}</h4> -->
                <!-- <span class="font-bold"
                  :class="($page.props.auth.user.promo_code && plan.account_type == 0) ? 'line-through text-2xl text-gray-500 mr-3' : 'text-primary text-4xl'">${{
                    plan.price }}
                </span> -->
                <!-- <span v-if="$page.props.auth.user.promo_code && plan.account_type == 0" class="text-4xl font-bold">${{
                  plan.price * 0.8 }}</span>
                <span class="text-xl text-gray-600" v-if="plan.account_type == 1 && plan.slug != 'Beginner'">/Employee x {{ form.count }} = ${{
                  plan.price * form.count }}</span>

                <h5 class="text-lg text-gray-500">
                  {{ plan.description }}
                </h5> -->
              </div>

              <!-- <ul class="flex-1 mb-6">
                <li v-for="service in plan.services.split('_')" class="flex mb-2 space-x-2">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                    class="flex-shrink-0 w-6 h-6">
                    <path fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"></path>
                  </svg>
                  <span>{{ service }}</span>
                </li>
              </ul> -->
              <div v-if="isPayment">
                <n-radio :checked="prevCard" :value="true" name="card-option" @change="handleChange(true)">
                  {{ $t('plans.UsePreviousCard') }}
                </n-radio>
                <n-radio :checked="!prevCard" :value="false" name="card-option" @change="handleChange(false)">
                  {{ $t('plans.UseNewCard') }}
                </n-radio>
              </div>
              <div :class="prevCard ? 'hidden' : ''">
                <div ref="card" class="p-2.5 rounded-md border-2 border-solid">
                  <!-- Elements will create input elements here -->
                </div>
                <div id="card-errors" class="mt-[0_!important] px-[15px] pt-1 text-red-600 font-sm" role="alert"></div>
              </div>
              <n-button type="button" :loading="disabled" class="bg-primary w-full text-white font-semibold"
                color="#FF6E2B" size="large" @click="Submit">
                {{ $t('plans.PayNow') }}
              </n-button>
              <div class="flex justify-center">
                <img class="w-10 mr-1" src="/imgs/credit_cards/visa.png" alt="visa">
                <img class="w-10 mr-1" src="/imgs/credit_cards/mastercard.png" alt="mastercard">
                <img class="w-10 mr-1" src="/imgs/credit_cards/jcb.png" alt="jcb">
                <img class="w-10" src="/imgs/credit_cards/american-express.png" alt="american-express">
              </div>
              <div class="text-center"><n-text class="italic text-gray-400">{{ $t('plans.Info') }}</n-text>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>