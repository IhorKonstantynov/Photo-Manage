<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { ArrowAltCircleRight } from '@vicons/fa';
import themeOverrides from "./themeOverrides";

const showingNavigationDropdown = ref(false);

const search = window.location.search;
</script>

<template>
  <n-config-provider :theme-overrides="themeOverrides">
    <div>
      <div class="min-h-screen bg-gradient-to-b from-[#FF761F] to-[#FF8E29]">
        <nav>
          <div class="text-center bg-black text-gray-400 py-1">
            <small>
              🚨 Due to high demand we had to reduce free usage until we upgrade ours servers. Sorry for the inconvenience.
            </small>
          </div>
          <!-- Primary Navigation Menu -->
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 relative">
              <div class="flex items-center flex-1">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                  <Link :href="route('landing') + search">
                  <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                  </Link>
                </div>

                <!-- Navigation Links -->
                <div class="hidden flex-1 justify-center space-x-10 sm:-my-px sm:ml-10 sm:flex">
                  <span class="font-semibold leading-tight text-white">
                    Word Genius
                  </span>
                  <span class="font-semibold leading-tight text-white">
                    Metaphor Genius
                  </span>
                  <span class="font-semibold leading-tight text-white">
                    Rhyme Genius
                  </span>
                  <span class="font-semibold leading-tight text-white">
                    Story Genius
                  </span>
                  <span class="font-semibold leading-tight text-white">
                    Homophone Genius
                  </span>
                </div>
              </div>

              <div class="hidden sm:flex sm:items-center sm:ml-6">
                <div class="ml-3 relative">
                  <Link :href="route('register') + search" class="ease-in duration-100 inline-flex items-center text-primary bg-white font-bold py-3 px-4 rounded hover:drop-shadow-lg hover:bg-black hover:text-white">
                    <n-icon class="mr-1">
                        <ArrowAltCircleRight />
                    </n-icon>
                    Get Started</Link>
                </div>
              </div>

              <!-- Hamburger -->
              <div class="-mr-2 flex items-center sm:hidden">
                <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                  class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-gray-primarys:outline-none transition duration-150 ease-in-out">
                  <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{
                      hidden: showingNavigationDropdown,
                      'inline-flex': !showingNavigationDropdown,
                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{
                      hidden: !showingNavigationDropdown,
                      'inline-flex': showingNavigationDropdown,
                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Responsive Navigation Menu -->
          <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
              <ResponsiveNavLink class="flex items-center" :href="route('register') + search" :active="true">
                <n-icon class="text-xl mr-2 text-inherit">
                  <ArrowAltCircleRight />
                </n-icon>
                Get Started
              </ResponsiveNavLink>
            </div>
          </div>
        </nav>

        <!-- Page Heading -->
        <header v-if="$slots.header">
          <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <slot name="header" />
          </div>
        </header>

        <!-- Page Content -->
        <main>
          <slot />
        </main>

        <!-- Logos -->
        <div class="max-w-4xl mx-auto py-5 flex justify-around flex-wrap">
          <img class="ease-in duration-300 w-4/12 md:w-1/5 p-5 sm:p-10 md:p-5 opacity-50 hover:opacity-100" src="/images/insurance-logo-1.webp" alt="logo1" />
          <img class="ease-in duration-300 w-4/12 md:w-1/5 p-5 sm:p-10 md:p-5 opacity-50 hover:opacity-100" src="/images/insurance-logo-2.webp" alt="logo1" />
          <img class="ease-in duration-300 w-4/12 md:w-1/5 p-5 sm:p-10 md:p-5 opacity-50 hover:opacity-100" src="/images/insurance-logo-3.webp" alt="logo1" />
          <img class="ease-in duration-300 w-4/12 md:w-1/5 p-5 sm:p-10 md:p-5 opacity-50 hover:opacity-100" src="/images/insurance-logo-4.webp" alt="logo1" />
          <img class="ease-in duration-300 w-4/12 md:w-1/5 p-5 sm:p-10 md:p-5 opacity-50 hover:opacity-100" src="/images/insurance-logo-5.webp" alt="logo1" />
        </div>
        <!-- Footer -->
        <footer class="bg-black  py-2">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-around md:justify-between h-16 relative flex-wrap items-center">
              <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                  <Link :href="route('landing') + search">
                    <ApplicationLogo class="mr-[100px] block h-8 w-auto fill-current text-gray-800" />
                  </Link>
                </div>

                <!-- Navigation Links -->
              </div>

              <div class="justify-center space-x-10 sm:-my-px sm:ml-10 flex">
                <small class="font-semibold leading-tight text-white">
                  {{ new Date().getFullYear() }} All Rights Reserved - LyricGenius
                </small>
              </div>

              <div class="flex sm:items-center sm:ml-6">
                <div class="ml-5 relative">
                  <Link :href="route('profile.plans') + search" class="text-white font-bold py-3 px-4 rounded">Pricing</Link>
                </div>
                <div class="ml-5 relative">
                  <Link :href="route('register') + search" class="text-white font-bold py-3 px-4 rounded">Contact</Link>
                </div>
              </div>
            </div>
          </div>
        </footer>
    </div>
  </div>
</n-config-provider></template>
