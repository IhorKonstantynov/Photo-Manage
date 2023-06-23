<script setup>
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { ArrowRight12Filled } from '@vicons/fluent';
import { ReturnUpBack, Close } from '@vicons/ionicons5';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
  photos: Array,
  photoEdit: Array,
});

const user = usePage().props.auth.user;
const showListModal = ref(false);
const showPhotosModal = ref(false);
const photosdata = ref([]);
const selPhoto = ref(null);

const handleShowListModal = () => {
  showListModal.value = true;
}

onMounted(() => {
});

const handleSelect = (photo) => {
  photosdata.value = JSON.parse(photo.result_urls);
  showPhotosModal.value = true;
  selPhoto.value = photo;
}

const handleBack = () => {
  showPhotosModal.value = false;
}

</script>

<template>
  <AuthenticatedLayout>

    <Head title="| Processing" />

    <template #header>
      <div class="max-w-5xl mt-2 mx-auto px-5 text-center py-3">
        <span class="text-xl md:text-2xl lg:text-3xl xl:text-4xl font-bold tracking-wider text-gray-800">{{ $t('home.AIBackgroundRemover') }}</span>
      </div>
    </template>
    <div class="max-w-5xl mx-auto flex justify-around items-center pb-10 flex-wrap">
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
        <h3 class="text-gray-400 italic mt-2 text-center">
          {{ $t('home.UnlimitedBackgroundOption') }}
        </h3>
      </div>
      <div class="w-full mt-5 text-center">
        <Link v-if="user.credit == 0" :href="route('charge.buyCredit')">
        <PrimaryButton class="mr-2 capitalize">
          {{ $t('home.RemoveBackground') }}
        </PrimaryButton>
        </Link>
        <PrimaryButton v-else class="mr-2 capitalize" @click="handleShowListModal">
          {{ $t('home.SelectPhoto') }}
        </PrimaryButton>
        <h3 class="text-gray-500 italic mt-2">
          {{ $t('home.YouHaveCredit', {credit: user.credit}) }}
        </h3>
        <div v-if="user.credit == 0" class="mt-6 max-w-md mx-auto">
          <n-alert title="" type="warning">
            {{ $t('home.Click') }} <Link class="text-blue-600 underline" :href="route('charge.buyCredit')">
            {{$t('common.here')}}
            </Link> {{ $t('home.toBuyCredit') }}
          </n-alert>
        </div>
      </div>
      <div class="w-full mt-5 flex flex-wrap px-4">
        <Link v-for="item in photoEdit.filter(p => p.bg_removed_img)" :href="route('photo.edit', {id: item.photo_id, index: item.original_img.split('/')[item.original_img.split('/').length - 2]})" class="w-1/5">
          <img :src="item.bg_removed_img" alt="rm" class="rounded shadow hover:border-2 cursor-pointer w-full">
        </Link>
      </div>
    </div>
    <n-modal v-model:show="showPhotosModal" >
      <n-card title="Select Photo!" :bordered="false" size="huge" role="dialog" aria-modal="true"
      style="width: 800px;">
        <!-- style="width: 600px; position: fixed; right: 100px; bottom: 100px" -->
        <template #header-extra>
          <n-button class="text-black" @click="handleBack" title="Back" circle>
            <template #icon>
              <n-icon>
                <ReturnUpBack />
              </n-icon>
            </template>
          </n-button>
        </template>
        <div class="flex justify-around flex-wrap">
          <div v-for="(url, index) in photosdata" class="w-[150px] h-[150px] cursor-pointer m-2">
            <Link class="w-full h-full" :href="route('photo.edit', {id: selPhoto.id, index: url.split('/')[url.split('/').length - 2]})">
              <img :src="url" alt=" " class="w-full h-full">
            </Link>
          </div>
        </div>
      </n-card>
    </n-modal>
    <n-modal v-model:show="showListModal" >
      <n-card title="Select Photo!" :bordered="false" size="huge" role="dialog" aria-modal="true"
      style="width: 600px;" 
      >
        <template #header-extra>
          <n-button class="text-black" @click="showListModal = false" circle>
            <template #icon>
              <n-icon>
                <Close />
              </n-icon>
            </template>
          </n-button>
        </template>
        <div class="flex justify-around flex-wrap">
          <div v-for="photo in photos" class="img-list w-[200px] h-[200px] relative cursor-pointer m-3" @click="handleSelect(photo)">
            <img :src="JSON.parse(photo.result_urls)[0]" alt=" " class="shadow border ease-in-out duration-100 w-full h-full absolute top-0 left-0">
            <img :src="JSON.parse(photo.result_urls)[1]" alt=" " class="shadow border ease-in-out duration-100 w-full h-full absolute top-0 left-0">
            <img :src="JSON.parse(photo.result_urls)[2]" alt=" " class="shadow border w-full h-full absolute top-0 left-0">
          </div>
          <div v-if="photos.length == 0">
            <h2>
              {{ $t('home.YouHaventAnyHeadshots') }} {{ $t('home.Click') }} 
              <Link :href="route('home')" class="text-blue-500 underline">{{ $t('common.here') }}</Link>
              {{ $t('home.toGenerate') }}
            </h2>
          </div>
        </div>
      </n-card>
    </n-modal>
  </AuthenticatedLayout>
</template>
