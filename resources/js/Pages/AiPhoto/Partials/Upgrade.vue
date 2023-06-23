<script setup>
import { ref, onMounted } from 'vue';
import { Head, usePage, Link, useForm } from '@inertiajs/vue3';
import { useClipboard } from '@vueuse/core';
import { Download, SendAltFilled } from '@vicons/carbon';
import { EyeRegular, CheckDouble, Copy, Plus } from '@vicons/fa';
import { CheckmarkDoneSharp } from '@vicons/ionicons5';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
  id: Number,
  message: String,
  canAdd: Boolean,
  highRes: Boolean,
});

const user = usePage().props.auth.user;
const showListModal = ref(false);
const form = useForm({});

onMounted(() => {
});

const referralLink = `https://prophotos.ai?utm_source=${user.name.replace(/ /g, '')}&utm_content=${user.id}`;

const { copy, copied } = useClipboard({
  copiedDuring: 4000,
});

const handleServiceShow = () => {
  showListModal.value = true;
}

const setHighRes = () => {
  form.put(route('photo.setHighRes', props.id), {
    onFinish: () => {
      console.log('finished');
    }
  });
}

</script>

<template>
  <div class="max-w-6xl mx-auto text-center bg-gray-100 pb-5 pt-5 px-2 sm:px-6 lg:px-8 mb-6">
    <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-600 leading-5 md:leading-4"
      v-html="$t('home.UpgradeTitle')">
    </h2>
    <div class="flex justify-center mt-4">
      <button
        class="ml-4 inline-flex items-center px-6 py-3 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 capitalize"
        @click="handleServiceShow">
        <!-- <n-icon class="text-lg mr-2">
                            <ShareSocialSharp />
                            </n-icon> -->
        <span>{{ $t('home.ViewOptions') }}</span>
      </button>
    </div>
    <div class="max-w-xl mx-auto mt-4">
      <n-alert v-if="message" title="Successfully added!" type="info" closable>
        {{ message }}
      </n-alert>
    </div>
  </div>
  <hr>
  <n-modal v-model:show="showListModal" preset="card" class="w-[600px] rounded">
    <template #header>
      <h2 class="text-center text-xl sm:text-2xl lg:text-3xl font-bold text-gray-600 leading-5">
        {{ $t('home.Upgrades') }}
      </h2>
    </template>
    <n-collapse accordion>
      <template #header-extra>
        <n-icon>
          <Plus />
        </n-icon>
      </template>
      <n-collapse-item name="2" v-if="canAdd">
        <template #header>
          <h2 class="text-sm sm:text-md lg:text-lg font-bold text-gray-600 leading-5 md:leading-4">
            {{ $t('plans.40MoreHeadshots', { photos: 40, price: 9.99 }) }}
          </h2>
        </template>
        <div>
          <!-- <h2 class="text-center text-md md:text-lg font-bold text-gray-500 leading-5 mb-4">
                                    Get the perfect photos for your holiday cards: <br />
                                    Thanksgiving, Chrismas, all included!
                                </h2> -->
          <div class="bg-gray-100 p-4 rounded flex flex-wrap">
            <!-- <img class="w-1/3 p-3" src="/imgs/original.png" alt="holiday card image" />
                                    <img class="w-1/3 p-3" src="/imgs/original.png" alt="holiday card image" />
                                    <img class="w-1/3 p-3" src="/imgs/original.png" alt="holiday card image" /> -->
            <div class="w-full text-center my-2">
              <Link :href="route('charge.buyMore', id)">
              <PrimaryButton class="px-[40px] py-[13px]">
                {{ $t('plans.Buy') }} $9.99
              </PrimaryButton>
              </Link>
            </div>
          </div>
        </div>
      </n-collapse-item>
      <n-collapse-item name="3">
        <template #header>
          <h2 class="text-sm sm:text-md lg:text-lg font-bold text-gray-600 leading-5 md:leading-4">
            {{ $t('home.AIBackgroundRemover') }}
          </h2>
        </template>
        <h2 class="text-center text-md md:text-lg font-bold text-gray-500 leading-5 mb-4 whitespace-pre-wrap">
          {{ $t('home.AIBackgroundRemoverTip') }}
        </h2>
        <div class="text-center bg-gray-100 rounded mb-5 py-5">
          <div class="flex justify-around items-center pb-10 flex-wrap">
            <img src="/imgs/original.png" class="w-1/4" alt="Original">
            <div class="flex items-center">
              <n-icon class="text-xl">
                <ArrowRight12Filled />
              </n-icon>
            </div>
            <img src="/imgs/nobg.png" class="w-1/4" alt="Original">
            <div class="flex items-center">
              <n-icon class="text-xl">
                <ArrowRight12Filled />
              </n-icon>
            </div>
            <div class="w-1/4">
              <img src="/imgs/changebg.png" class="w-full" alt="Original">
            </div>
            <div class="w-full mt-5 text-center">
              <Link v-if="user.credit == 0" :href="route('charge.buyCredit', { to: route('photos.edit') })">
              <PrimaryButton class="capitalize">
                {{ $t('plans.Buy') }} - $2.99
              </PrimaryButton>
              </Link>
              <Link v-else :href="route('photos.edit')">
              <PrimaryButton class="capitalize">
                {{ $t('home.RemoveBackground') }}
              </PrimaryButton>
              </Link>
            </div>
          </div>
          <!-- <Link :href="route('photos.edit')">
                            <PrimaryButton class="capitalize">AI Background Customizer</PrimaryButton>
                            </Link> -->
        </div>
      </n-collapse-item>
      <n-collapse-item name="1">
        <template #header>
          <h2 class="text-sm sm:text-md lg:text-lg font-bold text-gray-600 leading-5 md:leading-4"
            v-html="$t('home.WantFreeHeadshots', { count: 15 })">
          </h2>
        </template>
        <h2 class="text-center text-md md:text-lg font-bold text-gray-500 leading-5 mb-4">
          {{ $t('home.GetMoreShareSocial', { count: 15 }) }}
        </h2>
        <div class="bg-gray-100 p-4 rounded">
          <h3 class="text-sm sm:text-md lg:text-lg font-bold text-gray-900 leading-5">
            1. {{ $t('home.GetMoreShareSocialStep1') }}
          </h3>
          <!-- <h3 class="my-2 text-sm sm:text-md lg:text-lg font-bold text-gray-900 leading-5">
                                    2. Include 1 ~ 2 AI Headshots, https://prophotos.ai and the hashtags #aiheadshots #ai
                                    #prophotos
                                    <br>
                                    <br>
                                    &nbsp;&nbsp; Example : <PrimaryButton class="capitalize flex items-center"
                                        @click="copy('These headshots were 100% AI generated ! I made them at https://prophotos.ai #aiheadshots #ai #prophotos')">
                                        <n-icon class="mr-2">
                                            <CheckDouble class="animate-pulse" v-if="copied" />
                                            <Copy v-else />
                                        </n-icon>
                                        Copy Text
                                    </PrimaryButton>
                                    <br>
                                    <p class="italic m-2 p-2 bg-white rounded text-gray-500">These headshots were 100% AI
                                        generated ! I
                                      made them at https://prophotos.ai #aiheadshots #ai #prophotos</p>
                              </h3> -->
          <h3 class="my-2 text-sm sm:text-md lg:text-lg font-bold text-gray-900 leading-5">
            2. {{ $t('home.GetMoreShareSocialStep2') }}
          </h3>
          <h3 class="my-2 text-sm sm:text-md lg:text-lg font-bold text-gray-900 leading-5" v-html="`3. ${$t('home.GetMoreShareSocialStep3', { count: 20, time: 24 })}`">
          </h3>
          <div class="text-center m-4">
            <a :href="`mailto:hello@prophotos.ai?subject=Social Shared[${$page.props.auth.user.email}]`" target="_blank">
              <PrimaryButton class="capitalize flex items-center px-[30px]">
                <n-icon class="mr-2 text-xl">
                  <SendAltFilled />
                </n-icon>
                {{ $t('home.EmailUs') }}
              </PrimaryButton>
            </a>
          </div>
        </div>
      </n-collapse-item>
      <n-collapse-item name="4">
        <template #header>
          <h2 class="text-sm sm:text-md lg:text-lg font-bold text-gray-600 leading-5 md:leading-4">
            {{ $t('home.HighResolutioinDownloads') }}
          </h2>
        </template>
        <div>
          <h2 class="text-center text-md md:text-lg font-bold text-gray-500 leading-5 mb-4">
            {{ $t('home.HighResolutioinDownloadsTip') }}
          </h2>
          <div class="w-full text-center my-2">
            <!-- <Link :href="route('charge.buyMore', id)"> -->
            <PrimaryButton class="px-[40px] py-[13px]" :class="highRes && 'bg-blue-600'" @click="setHighRes">
              <span v-if="highRes">
                {{ $t('home.TurnOff') }}
              </span>
              <span v-else>
                {{ $t('home.TurnOn') }}
              </span>
            </PrimaryButton>
            <!-- </Link> -->
          </div>
        </div>
      </n-collapse-item>
      <!-- <n-collapse-item name="4">
                        <template #header>
                                <h2 class="text-sm sm:text-md lg:text-lg font-bold text-gray-600 leading-5 md:leading-4">
                                    Clothing Changes <span class="text-gray-400">(Coming Soon!)</span>
                                </h2>
                            </template>
                            <div>
                                <h2 class="text-center mb-3 text-lg sm:text-xl xl:text-2xl font-bold text-gray-300">
                                    Comming Soon!
                                </h2>
                            </div>
                        </n-collapse-item> -->
      <!-- <n-collapse-item name="5">
                            <template #header>
                                <h2 class="text-sm sm:text-md lg:text-lg font-bold text-gray-600 leading-5 md:leading-4">
                                    Holiday Photos <span class="text-gray-400">(Coming Soon!)</span>
                                </h2>
                            </template>
                            <div>
                                <h2 class="text-center mb-3 text-lg sm:text-xl xl:text-2xl font-bold text-gray-300">
                                    Comming Soon!
                                </h2>
                            </div>
                        </n-collapse-item> -->
      <!-- <n-collapse-item name="6">
                            <template #header>
                                <h2 class="text-sm sm:text-md lg:text-lg font-bold text-gray-600 leading-5 md:leading-4">
                                    Change Eye Color <span class="text-gray-400">(Coming Soon!)</span>
                                </h2>
                            </template>
                            <div>
                                <h2 class="text-center mb-3 text-lg sm:text-xl xl:text-2xl font-bold text-gray-300">
                                    Comming Soon!
                                </h2>
                            </div>
                        </n-collapse-item> -->
      <n-collapse-item name="7">
        <template #header>
          <h2 class="text-sm sm:text-md lg:text-lg font-bold text-gray-600 leading-5 md:leading-4">
            {{ $t('home.RefferalCodeMakeSale', {percent: 20}) }}
          </h2>
        </template>
        <div class="mx-auto flex flex-wrap px-4 justify-around items-center py-4">
          <h2 class="w-full text-center text-md md:text-lg font-bold text-gray-500 leading-5 mb-4">
            {{ $t('home.EarnCommissionPerSale', {percent: 20}) }}
          </h2>
          <div class="flex flex-1 items-center">
            <TextInput id="referral" type="text"
              class="flex-1 cursor-pointer bg-gray-100 opacity-70 border focus:shadow-none" readonly
              @click="copy(referralLink)" :value="referralLink" />
            <PrimaryButton @click="copy(referralLink)" class="ml-4 capitalize py-3 flex items-center focus:shadow-none">
              <n-icon>
                <CheckmarkDoneSharp v-if="copied" class="animate-pulse" />
                <Copy v-else />
              </n-icon>
              <span class="ml-2 hidden sm:inline">{{ $t('home.CopyLink') }}</span>
            </PrimaryButton>
          </div>
        </div>
      </n-collapse-item>
    </n-collapse>
  </n-modal>
</template>
