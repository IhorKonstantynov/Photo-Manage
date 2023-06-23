<script setup>
import { ref, onMounted } from 'vue';
import Spinner from '@/Components/Spinner.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

const props = defineProps({
    photo: Object
});

const progress = ref(props.photo.count == 0 ? 1 : props.photo.count * 10);
const user = usePage().props.auth.user;

onMounted(() => {
    window.io.on('connect', () => {
        console.log('connected to server');
    });

    window.io.on('processing', (data) => {
        console.log(data, 'secket data.');
        if (data.userId == user.id) {
            progress.value = parseInt(100 / (data.length)) * data.count;
        };
    });
});

</script>

<template>
    <AuthenticatedLayout>

        <Head title="| Processing" />

        <template #header>
            <div class="max-w-5xl mt-10 mx-auto px-5 text-center bg-gray-100 py-20">
                <div class="text-center">
                    <span class="loader"></span>
                </div>
                <span class="text-2xl font-bold tracking-wider text-red-600">{{ $t('home.LoadingTitle') }}</span>
                <h2 class="font-bold text-md text-red-600 mb-5">{{ $t('home.LoadingDescription') }}</h2>
                <h2 class="text-md text-red-600 mb-5">{{ $t('home.LoadingTip') }}</h2>
                <n-progress type="line" :percentage="progress" :border-radius="5" :fill-border-radius="0" :indicator-placement="'inside'" processing />
            </div>
        </template>
        <div class="max-w-5xl mx-auto flex justify-center pb-10 flex-wrap">
        </div>
    </AuthenticatedLayout>
</template>
