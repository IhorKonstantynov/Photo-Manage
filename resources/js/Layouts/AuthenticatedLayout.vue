<script setup>
import { ref, onMounted, h, computed } from 'vue';
import { NIcon } from 'naive-ui';
import { Link, usePage, useForm } from '@inertiajs/vue3';
import { useClipboard } from '@vueuse/core';
import { trans } from "laravel-vue-i18n";
import { PersonCircleSharp, LogOut, Home, CheckmarkDoneSharp } from '@vicons/ionicons5';
import { UserSettings, CalendarSettings } from '@vicons/carbon';
import { Users, Copy, Image, CloudUploadAlt, BuildingRegular, HandHoldingUsd, CaretDown, ChartLine, ChartPie } from '@vicons/fa';
import { ImageEdit24Regular } from '@vicons/fluent';

import TextInput from '@/Components/TextInput.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import themeOverrides from "./themeOverrides";

const showingNavigationDropdown = ref(false);
const user = usePage().props.auth.user;
const form = useForm({});

const options = computed({
    get() {
        return [
            {
                label: trans("common.Upload"),
                key: "Upload",
                icon: CloudUploadAlt,
                href: 'home',
            },
            {
                label: trans("home.YourHeadshots"),
                key: "Your Headshots",
                icon: Image,
                href: 'photos',
            },
            {
                label: trans('home.BGRemoveTool'),
                key: "BG Remove Tool",
                icon: ImageEdit24Regular,
                href: 'photos.edit',
            },
            {
                label: trans("enterprise.Employees"),
                key: "Employees",
                icon: Users,
                href: 'enterprise.employees',
                hidden: user.account_type != 1
            },
            {
                label: trans("enterprise.Enterprise"),
                key: "Enterprise",
                icon: BuildingRegular,
                href: 'user.enterprises',
                hidden: !(user.account_type == 0 && user.available_employee == -1)
            },
            {
                label: trans("refferals.Refferals"),
                key: "Referrals",
                icon: HandHoldingUsd,
                href: 'referrals',
            },
            {
                label: trans("profile.Profile"),
                key: "Profile",
                icon: UserSettings,
                href: 'profile.edit',
            },
            {
                label: trans("plans.Plans"),
                key: "Plans",
                icon: CalendarSettings,
                href: 'profile.plans',
            },
            {
                label: trans("profile.Admins"),
                key: "Admins",
                icon: Users,
                hidden: user.permission != 1,
                children: [
                    {
                        label: trans("home.Dashboard"),
                        key: "Dashboard",
                        icon: ChartPie,
                        href: 'admin.dashboard',
                    },
                    {
                        label: "Users",
                        key: "Users",
                        icon: Users,
                        href: 'admin.users',
                    },
                    {
                        label: trans("home.Anyalyze"),
                        key: "Anyalyze",
                        icon: ChartLine,
                        href: 'admin.analyze',
                    }
                ]
            },
            {
                label: trans("auth.LogOut"),
                key: "Log Out",
                icon: LogOut,
                href: 'logout',
                method: 'POST',
            },
        ]
    }
});

const referralLink = `https://prophotos.ai?utm_source=${user.name.replace(/ /g, '')}&utm_content=${user.id}`;

const { copy, copied } = useClipboard({
    copiedDuring: 4000,
});

onMounted(() => {
    window.io.on('connect', () => {
        console.log('connected to server');
    });

    window.io.on('created_tune', (data) => {
        console.log(data, 'secket data.');
        if (data.userId == user.id) {
            form.get(route('photo.gallery', { id: data.id, type: "result" }));
        };
    });
});

const handleCopy = () => {
    copy(referralLink);
}

const renderDropdownLabel = (option) => {
    if (option.type === "group" || option.children || !option.href) {
        return h('span', { class: 'text-primary' }, { default: () => option.label });
    }
    return h(
        Link,
        {
            class: '!text-primary',
            href: route(option.href),
            method: option.method
        },
        {
            default: () => option.label
        }
    );
}
const renderDropdownIcon = (option) => {
    return h(NIcon, {
        class: 'text-primary',
    }, {
        default: () => h(option.icon)
    });
}

</script>

<template>
    <n-config-provider :theme-overrides="themeOverrides">
        <div>
            <div class="min-h-screen bg-white">
                <nav class="bg-white border-b">
                    <!-- Primary Navigation Menu -->
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16 relative">
                            <div class="flex">
                                <!-- Logo -->
                                <div class="shrink-0 flex items-center">
                                    <Link :href="user.count > 0 ? route('photos') : route('home')">
                                    <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                                    </Link>
                                </div>
                            </div>

                            <div class="hidden sm:flex sm:items-center sm:ml-6">
                                <!-- Settings Dropdown -->
                                <n-dropdown :options="options.filter(option => !option.hidden)" placement="bottom-start"
                                    trigger="click" :render-label="renderDropdownLabel" :render-icon="renderDropdownIcon">
                                    <n-button class="bg-primary rounded">
                                        <template #icon>
                                            <n-icon class="text-xl mr-1">
                                                <PersonCircleSharp />
                                            </n-icon>
                                        </template>
                                        {{ $page.props.auth.user.name }}
                                        <n-icon class="ml-1">
                                            <CaretDown />
                                        </n-icon>
                                    </n-button>
                                </n-dropdown>
                            </div>

                            <!-- Hamburger -->
                            <div class="-mr-2 flex items-center sm:hidden">
                                <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                                    class="inline-flex items-center justify-center p-2 rounded-md text-primary hover:bg-gray-primarys:outline-none transition duration-150 ease-in-out">
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16" />
                                        <path :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Responsive Navigation Menu -->
                    <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                        class="sm:hidden">
                        <div class="pt-2 pb-3 space-y-1">
                            <ResponsiveNavLink class="flex items-center" :href="route('home')"
                                :active="route().current('home')" v-if="user.plan_id || user.available_employee > 0">
                                <n-icon class="text-xl mr-2 text-inherit">
                                    <CloudUploadAlt />
                                </n-icon>
                                {{ $t('common.Upload') }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink class="flex items-center" :href="route('photos')"
                                :active="route().current('photos')">
                                <n-icon class="text-xl mr-2 text-inherit">
                                    <Image />
                                </n-icon>
                                {{ $t('home.YourHeadshots') }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink class="flex items-center" :href="route('photos.edit')"
                                :active="route().current('photos.edit')">
                                <n-icon class="text-xl mr-2 text-inherit">
                                    <ImageEdit24Regular />
                                </n-icon>
                                {{ $t('home.BGRemoveTool') }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink class="flex items-center" :href="route('referrals')"
                                :active="route().current('referrals')">
                                <n-icon class="text-xl mr-2 text-inherit">
                                    <HandHoldingUsd />
                                </n-icon>
                                {{ $t('refferals.Referrals') }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.permission == 1" class="flex items-center"
                                :href="route('admin.dashboard')" :active="route().current('admin.dashboard')">
                                <n-icon class="text-xl mr-2 text-inherit">
                                    <ChartPie />
                                </n-icon>
                                {{ $t('home.Dashboard') }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.permission == 1" class="flex items-center"
                                :href="route('admin.users')" :active="route().current('admin.users')">
                                <n-icon class="text-xl mr-2 text-inherit">
                                    <Users />
                                </n-icon>
                                {{ $t('home.Users') }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.permission == 1" class="flex items-center"
                                :href="route('admin.analyze')" :active="route().current('admin.analyze')">
                                <n-icon class="text-xl mr-2 text-inherit">
                                    <ChartLine />
                                </n-icon>
                                {{ $t('home.Analyze') }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.account_type == 1" class="flex items-center"
                                :href="route('enterprise.employees')" :active="route().current('enterprise.employees')">
                                <n-icon class="text-xl mr-2 text-inherit">
                                    <Users />
                                </n-icon>
                                {{ $t('enterprise.Employees') }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-else-if="$page.props.auth.user.available_employee == -1"
                                class="flex items-center" :href="route('user.enterprises')"
                                :active="route().current('user.enterprises')">
                                <n-icon class="text-xl mr-2 text-inherit">
                                    <BuildingRegular />
                                </n-icon>
                                {{ $t('enterprise.Enterprise') }}
                            </ResponsiveNavLink>
                        </div>

                        <!-- Responsive Settings Options -->
                        <div class="pt-4 pb-2 border-t border-b border-gray-200">
                            <div class="px-4">
                                <div class="font-medium text-base text-primary">
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
                                    {{ $t('profile.Profile') }}
                                </ResponsiveNavLink>
                                <ResponsiveNavLink class="flex items-center" :href="route('profile.plans')"
                                    :active="route().current('profile.plans')">
                                    <n-icon class="text-xl mr-2">
                                        <CalendarSettings />
                                    </n-icon>
                                    {{ $t('plans.Plans') }}
                                </ResponsiveNavLink>
                                <ResponsiveNavLink class="flex items-center" :href="route('logout')" method="post"
                                    as="button">
                                    <n-icon class="text-xl mr-2">
                                        <LogOut />
                                    </n-icon>
                                    {{ $t('auth.LogOut') }}
                                </ResponsiveNavLink>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Referral Link -->
                <section class="border-b" v-if="route().current('photo.gallery') || route().current('referrals')">
                    <div class="max-w-3xl mx-auto flex flex-wrap px-4 justify-around items-center py-4">
                        <p class="font-bold text-gray-500 px-4">{{ $t('home.EarnCommissionPerSale', { percent: 20 }) }}</p>
                        <div class="flex flex-1 items-center">
                            <TextInput id="referral" type="text"
                                class="flex-1 cursor-pointer bg-gray-100 opacity-70 border focus:shadow-none" readonly
                                @click="handleCopy" :value="referralLink" />
                            <PrimaryButton @click="handleCopy"
                                class="ml-4 capitalize py-3 flex items-center focus:shadow-none">
                                <n-icon>
                                    <CheckmarkDoneSharp v-if="copied" />
                                    <Copy v-else />
                                </n-icon>
                                <span class="ml-2 hidden sm:inline">{{ $t('home.CopyLink') }}</span>
                            </PrimaryButton>
                        </div>
                    </div>
                </section>
                <!-- Page Heading -->
                <header v-if="$slots.header">
                    <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Page Content -->
                <main>
                    <slot />
                </main>
            </div>
        </div>
    </n-config-provider>
</template>
