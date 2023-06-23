<script setup>
import { ref, h } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
// import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { WalletSharp, PersonCircleSharp, LogOut } from '@vicons/ionicons5';
import { UserSettings, CalendarSettings } from '@vicons/carbon';
import { Home } from '@vicons/ionicons5';
import themeOverrides from "./themeOverrides";
import { NIcon } from "naive-ui";

const showingNavigationDropdown = ref(false);

function renderIcon(icon) {
  return () => h(NIcon, null, { default: () => h(icon) });
}

const menuOptions = [
  {
    label: "Users",
    key: "users",
    icon: renderIcon(UserSettings)
  },
];

</script>

<template>
  <n-config-provider :theme-overrides="themeOverrides">
    <div>
    <div class="min-h-screen bg-primary">
      <nav>
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 relative">
              <div></div>

              <div class="flex absolute top-1/2 left-1/2 -translate-y-1/2 -translate-x-1/2">
              <!-- Logo -->
              <div class="shrink-0 flex items-center">
                <Link :href="route('home')">
                <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                </Link>
              </div>

                <!-- Navigation Links -->
                <!-- <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                        <NavLink :href="route('home')" :active="route().current('home')">
                                            Home
                                        </NavLink>
                                    </div> -->
              </div>

              <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- <div class="mr-6 text-primary bg-white px-3 py-2 rounded-full cursor-pointer">
                                                <span class="text-md flex items-center">
                                                    <n-icon class="text-xl mr-2">
                                                        <WalletSharp />
                                                    </n-icon>
                                                    : <span class="ml-2">{{ $page.props.auth.user.balance.toFixed(2) }}</span>
                                                </span>
                                            </div> -->
                <!-- Settings Dropdown -->
                <div class="ml-3 relative">
                  <Dropdown align="right" width="48">
                    <template #trigger>
                      <span class="inline-flex rounded-md">
                        <button type="button"
                          class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-primary bg-white focus:outline-none transition ease-in-out duration-150">
                          <n-icon class="text-xl mr-1">
                            <PersonCircleSharp />
                          </n-icon>
                          {{ $page.props.auth.user.name }}
                          <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                              clip-rule="evenodd" />
                          </svg>
                        </button>
                      </span>
                    </template>

                    <template #content>
                      <DropdownLink class="text-primary flex items-center" :href="route('profile.edit')">
                        <n-icon class="text-xl mr-2">
                          <UserSettings />
                        </n-icon>
                        Profile
                      </DropdownLink>
                      <DropdownLink class="text-primary flex items-center" :href="route('profile.plans')">
                        <n-icon class="text-xl mr-2">
                          <CalendarSettings />
                        </n-icon>
                        Plans
                      </DropdownLink>
                      <DropdownLink class="text-primary flex items-center" :href="route('logout')" method="post"
                        as="button">
                        <n-icon class="text-xl mr-2">
                          <LogOut />
                        </n-icon>
                        Log Out
                      </DropdownLink>
                    </template>
                  </Dropdown>
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
              <ResponsiveNavLink class="flex items-center" :href="route('home')" :active="route().current('home')">
                <n-icon class="text-xl mr-2 text-inherit">
                  <Home />
                </n-icon>
                Home
              </ResponsiveNavLink>
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-2 border-t border-b border-gray-200">
              <div class="px-4">
                <div class="font-medium text-base text-white">
                  {{ $page.props.auth.user.name }}
                </div>
                <div class="font-medium text-sm text-gray-800">{{ $page.props.auth.user.email }}</div>
              </div>

              <div class="mt-3 space-y-1">
                <ResponsiveNavLink class="flex items-center" :href="route('profile.edit')"
                  :active="route().current('profile.edit')">
                  <n-icon class="text-xl mr-2">
                    <UserSettings />
                  </n-icon>
                  Profile
                </ResponsiveNavLink>
                <ResponsiveNavLink class="flex items-center" :href="route('profile.plans')"
                  :active="route().current('profile.plans')">
                  <n-icon class="text-xl mr-2">
                    <CalendarSettings />
                  </n-icon>
                  Plans
                </ResponsiveNavLink>
                <ResponsiveNavLink class="flex items-center" :href="route('logout')" method="post" as="button">
                  <n-icon class="text-xl mr-2">
                    <LogOut />
                  </n-icon>
                  Log Out
                </ResponsiveNavLink>
              </div>
            </div>
          </div>
        </nav>
        <n-layout>
          <n-layout>
            <!-- <n-layout-sider bordered show-trigger collapse-mode="width" :collapsed-width="64" :width="200" :native-scrollbar="false" style="min-height: calc(100vh - 64px)">
              <n-menu :collapsed-width="64" :collapsed-icon-size="22" :options="menuOptions" />
            </n-layout-sider> -->
            <n-layout>
              <slot />
            </n-layout>
          </n-layout>
        </n-layout>
      </div>
    </div>
  </n-config-provider>
</template>
