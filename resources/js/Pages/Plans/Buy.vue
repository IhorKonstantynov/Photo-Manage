<script setup>
import { ref, onMounted } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

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
  photoId: Number,
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
    })).post(route('charge.payMore', props.photoId), {
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
      })).post(route('charge.payMore', props.photoId), {
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
        <h2 class="text-xl font-bold lg:text-2xl text-primary">{{ $t('plans.BuyTitle') }}</h2>
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
                  <!-- <n-select v-model:value="form.count" :options="data" size="large" placeholder="Credit amount" /> -->
                  <h2 class="text-sm sm:text-md lg:text-lg font-bold text-gray-600 leading-5 md:leading-4">
                    {{ $t('plans.40MoreHeadshots', {photos: 40, price: 9.99}) }}
                  </h2>
                </div>
              </div>
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
                {{ $t('PayNow') }}
              </n-button>
              <div class="flex justify-center">
                <img class="w-10 mr-1" src="/imgs/credit_cards/visa.png" alt="visa">
                <img class="w-10 mr-1" src="/imgs/credit_cards/mastercard.png" alt="mastercard">
                <img class="w-10 mr-1" src="/imgs/credit_cards/jcb.png" alt="jcb">
                <img class="w-10" src="/imgs/credit_cards/american-express.png" alt="american-express">
              </div>
              <div class="text-center"><n-text class="italic text-gray-400">{{ plans.Info }}</n-text>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>