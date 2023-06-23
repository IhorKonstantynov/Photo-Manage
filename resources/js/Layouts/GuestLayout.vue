<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import themeOverrides from './themeOverrides';

</script>

<template>
    <n-config-provider :theme-overrides="themeOverrides">
        <div class="min-h-screen bg-white">
            <nav class="bg-white border-b">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16 relative">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('home')">
                                <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                                </Link>
                            </div>
                        </div>
                        <!-- <div> Language </div> -->
                    </div>
                </div>
            </nav>
            <div class="bg-[url('/imgs/effect_bg.png')] bg-no-repeat bg-center bg-contain flex flex-col sm:justify-center items-center pt-6 px-2 sm:pt-0" style="min-height: calc(100vh - 70px);">
                <h1 class="text-2xl md:text-3xl lg:text-4xl xl:text-5xl font-bold text-center my-3 whitespace-pre-wrap">
                    {{ $t('auth.LoginTitle') }}
                </h1>
                <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white border border-solid border-gray-300 shadow-md sm:rounded-lg">
                    <slot />
                </div>
            </div>
        </div>
    </n-config-provider>
</template>
