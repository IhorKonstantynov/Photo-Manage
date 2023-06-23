<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import moment from 'moment';
import { Head, Link } from '@inertiajs/vue3';
import { OpenOutline } from '@vicons/ionicons5'
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    photos: Array,
    user: Object,
});

const type = ['info', 'info', 'success', 'error', 'info'];

</script>

<template>
    <Head title="| Gallery" />
    <AuthenticatedLayout>
        <template #header>
            <div class="max-w-6xl mx-auto text-center bg-white pb-10 pt-3 px-2 sm:px-6 lg:px-8">
                <h2 class="text-xl sm:text-2xl lg:text-4xl xl:text-5xl font-bold text-gray-600">
                    <span v-if="user">{{ user.name }}'s</span><span v-else>Your</span> AI Headshots
                </h2>
                <div class="text-center bg-gray-100 rounded mt-5 py-5">
                    <h2 class="mb-3 text-lg sm:text-xl lg:text-2xl xl:text-3xl font-bold text-gray-700">
                        {{ $t('home.TryOurAIBGRemoverCustomizer') }}
                    </h2>
                    <Link :href="route('photos.edit')">
                    <PrimaryButton class="capitalize">{{ $t('home.AIBGCustomizer') }}</PrimaryButton>
                    </Link>
                </div>
            </div>
        </template>
        <div>
            <div class="max-w-6xl flex flex-wrap justify-around mx-auto pb-10 px-4 sm:px-8 lg:px-8">
                <div class="w-[70%] sm:w-1/2 md:w-1/4 lg:w-1/4 xl:w-1/5 px-2" v-for="(photo, index) in photos" :key="index">

                    <vue-flip width="100%" height="340px" active-hover>
                        <template #front>
                            <div class="rounded overflow-hidden shadow-lg border">
                                <n-carousel class="w-full h-48" autoplay :show-dots="false">
                                    <img class="carousel-img object-cover w-full h-full"
                                        v-for="url in JSON.parse(photo.urls)" :src="`${url}`" alt="AI Images" :key="url">
                                </n-carousel>
                                <n-badge
                                    :value="(photo.status == 4 || photo.status == 5 || photo.status == 6) ? 'Refunded' : `$${photo.plan.price}`"
                                    :type="type[photo.plan_id]">
                                    <div class="px-6 pt-4 pb-2">
                                        <div class="font-bold text-xl mb-2 text-gray-700">{{ $t('home.inputs') }} {{
                                            $t('Images') }}
                                        </div>
                                        <p class="text-gray-700 text-[12px] m-0">
                                            {{ moment(photo.created_at).format('YYYY-MM-DD HH:mm:ss') }}
                                            <span v-if="$page.props.auth.user.permission == 1 && photo.retouch == 1"> ✅
                                            </span>
                                        </p>
                                    </div>
                                </n-badge>
                                <div v-if="$page.props.auth.user.permission == 1" class="px-6 pb-2">
                                    <Link class="text-primary no-underline flex items-center text-sm"
                                        :href="route('photo.gallery', { id: photo.id, type: 'inputs' })">
                                    <n-icon class="mr-2">
                                        <OpenOutline />
                                    </n-icon> {{ $t('home.inputs') }} {{ $t('Images') }}</Link>
                                    <Link class="text-primary no-underline flex items-center text-sm"
                                        :href="route('photo.gallery', { id: photo.id, type: 'cropped' })">
                                    <n-icon class="mr-2">
                                        <OpenOutline />
                                    </n-icon>
                                    {{ $t('home.cropped') }} {{ $t('Images') }}
                                    </Link>
                                </div>
                            </div>
                        </template>
                        <template #back>
                            <div class="rounded overflow-hidden shadow-lg border">
                                <div class="w-full h-48 relative">
                                    <n-carousel
                                        v-if="photo.result_urls && ((photo.status != 4 && photo.status != 5 && photo.status != 6) || $page.props.auth.user.permission == 1)"
                                        class="w-full h-48" autoplay :show-dots="false">
                                        <img class="carousel-img object-cover w-full h-full"
                                            v-for="url in JSON.parse(photo.result_urls).slice(0, 3)" :src="url"
                                            alt="AI Images" :key="url">
                                    </n-carousel>
                                    <Link title="Go to progressing page." v-else-if="!photo.result_urls"
                                        class="absolute top-0 left-0 w-full h-full flex items-center justify-center"
                                        :href="route('photo.processing', photo.id)">
                                    <span class="loader"></span>
                                    </Link>
                                    <div v-else
                                        class="absolute top-0 left-0 w-full h-full flex items-center justify-center bg-gray-400">
                                        {{ $t('plans.Refunded') }} </div>
                                    <Link
                                        v-if="photo.result_urls && ((photo.status != 4 && photo.status != 5 && photo.status != 6) || $page.props.auth.user.permission == 1)"
                                        class="absolute top-0 right-0 w-full h-full flex items-center justify-center text-white"
                                        :href="route('photo.gallery', { id: photo.id, type: 'result' })">
                                    <n-icon class="text-2xl">
                                        <OpenOutline />
                                    </n-icon>
                                    </Link>
                                </div>
                                <n-badge
                                    :value="(photo.status == 4 || photo.status == 5 || photo.status == 6) ? $t('plans.Refunded') : `$${photo.plan.price}`"
                                    :type="type[photo.plan_id]">
                                    <div class="px-6 pt-4 pb-2">
                                        <div class="font-bold text-xl mb-2 text-gray-700">{{ $t('home.AIHeadshots') }}</div>
                                        <p class="text-gray-700 text-[12px] m-0">
                                            {{ moment(photo.created_at).format('YYYY-MM-DD HH:mm:ss') }}
                                        </p>
                                    </div>
                                </n-badge>
                                <div v-if="$page.props.auth.user.permission == 1" class="px-6 pb-2">
                                    <Link class="text-primary no-underline flex items-center text-sm"
                                        :href="route('photo.gallery', { id: photo.id, type: 'inputs' })">
                                    <n-icon class="mr-2">
                                        <OpenOutline />
                                    </n-icon>
                                    {{ $t('home.inputs') }} {{ $t('Images') }}
                                    </Link>
                                    <Link class="text-primary no-underline flex items-center text-sm"
                                        :href="route('photo.gallery', { id: photo.id, type: 'cropped' })">
                                    <n-icon class="mr-2">
                                        <OpenOutline />
                                    </n-icon>
                                    {{ $t('home.cropped') }} {{ $t('Images') }}
                                    </Link>
                                </div>
                            </div>
                        </template>
                    </vue-flip>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
