<script setup>
import { ref, onMounted, watch } from 'vue';
import { Head, usePage, Link, router } from '@inertiajs/vue3';
import { saveAs } from 'file-saver';
import moment from 'moment';
import swal from 'sweetalert';
import { fabric } from 'fabric';
import { CloudUploadAlt, Save } from '@vicons/fa'
import { Download } from '@vicons/carbon'
// import { ArrowForwardCircle } from '@vicons/ionicons5'

// import Spinner from '@/Components/Spinner.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    editPhoto: Object,
    csrfToken: String,
});

const result_url = ref(props.editPhoto.bg_removed_img);
const canvasRef = ref(null);
const uploadRef = ref(null);
const canvas = ref(null);
const loading = ref(false);
const remvingBG = ref(false);
const saving = ref(false);
const selectedBg = ref(props.editPhoto.bg_custom_status);
console.log(props.editPhoto)
const exData = ref({
    _token: props.csrfToken
});

onMounted(() => {
    canvas.value = new fabric.Canvas(canvasRef.value);
    canvas.value.setDimensions({
        width: 512,
        height: 512
    }, {
        backstoreOnly: true,
    });
    canvas.value.on('after:render', (event) => {
        handleSave();
    });
    if (props.editPhoto.edit_img) {
        canvas.value.loadFromJSON(JSON.parse(props.editPhoto.edit_img), () => {
            canvas.value.item(0).set({
                transparentCorners: false,
                hasControls: false,
                selectable: false,
            });
            canvas.value.item(1).set({
                transparentCorners: false,
                hasControls: false,
                selectable: false,
            });
            canvas.value.renderAll();
        });
    } else {
        fabric.Image.fromURL(selectedBg.value == -1 ? props.editPhoto.bg_custom : `/imgs/photo_bg/BG-${Number(selectedBg.value)}.png`, function (oImg) {
            // scale image down, and flip it, before adding it onto canvas
            let min = Math.min(oImg.width, oImg.height);
            oImg.scale(512 / min);
            oImg.set({
                transparentCorners: false,
                hasControls: false,
                selectable: false,
            });
            canvas.value.add(oImg);
            canvas.value.renderAll();

            if (result_url.value) {
                fabric.Image.fromURL(result_url.value, function (rImg) {
                    // scale image down, and flip it, before adding it onto canvas
                    rImg.scale(512 / rImg.width);
                    rImg.set({
                        transparentCorners: false,
                        hasControls: false,
                        selectable: false,
                    })
                    canvas.value.add(rImg);
                    handleActive(1);
                });
            }
        });
    }
});

// watch(() => props.editPhoto.bg_custom, () => {
//     handleBG(-1);
// });

const handleClick = () => {
    remvingBG.value = true;
    axios.post(route('photo.removeBg'), {
        ...exData.value,
        id: props.editPhoto.id,
    }).then((res) => {
        result_url.value = res.data.path;
        fabric.Image.fromURL(result_url.value, function (rImg) {
            rImg.scale(512 / rImg.width);
            rImg.set({
                transparentCorners: false,
                hasControls: false,
                selectable: false,
            })
            canvas.value.add(rImg);
            handleActive(1);
        });
    }).finally(() => {
        remvingBG.value = false;
        router.reload();
    });
}

const handleBG = (i) => {
    selectedBg.value = i;
    if (i == -1) {
        axios.put(route('photo.bg.status'), {
            ...exData.value,
            id: props.editPhoto.id,
            status: -1,
        });
    } else {
        axios.put(route('photo.bg.status'), {
            ...exData.value,
            id: props.editPhoto.id,
            status: i,
        });
    }
    canvas.value.item(0).setSrc(i == -1 ? props.editPhoto.bg_custom : `/imgs/photo_bg/BG-${i}.png`, () => {
        let oImg = canvas.value.item(0);
        let min = Math.min(oImg.width, oImg.height);
        oImg.scale(512 / min);
        canvas.value.renderAll();
    });
}

const handleActive = (ind) => {
    // canvas.value.setActiveObject(canvas.value.item(ind));
    canvas.value.renderAll();
}

const handleDownload = () => {
    const dataURL = canvas.value.toDataURL({
        format: 'jpg',
        quality: 1
    });
    saveAs(dataURL, `my-photo_${moment().format('YYYY-MM-DD_HH:mm:ss')}.jpg`);
}

const handleRDownload = () => { 
    if(!result_url.value) return;
    saveAs(result_url.value, `my-rmbg-photo_${moment().format('YYYY-MM-DD_HH:mm:ss')}.png`);
}

const handleBefore = () => {
    console.log('beofore');
    loading.value = true;
    return true;
}

const handleSuccess = () => {
    loading.value = false;
    router.reload();
}

const handleError = () => {
    loading.value = false;
}

const handleSave = () => {
    // console.log();
    if (!canvas.value.item(1)) {
        return;
    }
    saving.value = true;
    axios.put(route('photo.edit.save'), {
        ...exData.value,
        id: props.editPhoto.id,
        json_data: JSON.stringify(canvas.value.toJSON()),
    }).then(() => {
        // swal("Good job!", "Successfully saved!", "success");
    }).finally(() => {
        saving.value = false;
    });
}

</script>

<template>
    <AuthenticatedLayout>

        <Head title="| Edit" />

        <template #header>
            <div class="max-w-5xl mt-2 mx-auto px-5 text-center">
                <span class="text-2xl font-bold tracking-wider text-red-600">{{ $t('home.RemoveBackground') }}</span>
                <div v-if="$page.props.auth.user.credit == 0" class="mt-6 max-w-md mx-auto">
                    <n-alert title="" type="warning">
                        {{ $t('home.Click') }} <Link class="text-blue-600 underline" :href="route('charge.buyCredit')"> {{ $t('common.here') }} </Link> {{ $t('home.toBuyCredit') }}
                    </n-alert>
                </div>
            </div>
        </template>
        <div class="max-w-5xl mx-auto flex justify-center items-center pb-10 flex-wrap">
            <img class="w-[300px] m-2" :src="editPhoto.original_img" alt="Edit Photo">
            <!-- <div class="flex items-center">
                <n-icon class="text-3xl">
                    <ArrowForwardCircle />
                </n-icon>
            </div> -->
            <div class="w-[300px] h-[300px] m-2 bg-[url('/imgs/transparent_bg.jpg')] relative">
                <img v-if="result_url" class="w-[300px]" :src="result_url" alt="Removed Photo.">
                <div class="absolute top-0 left-0 w-full h-full flex items-center justify-center hover:bg-[#0008] opacity-0 hover:opacity-100 cursor-pointer" @click="handleRDownload">
                    <n-icon class="text-white text-2xl">
                        <Download />
                    </n-icon>
                </div>
            </div>
            <div class="w-full text-center mt-3">
                <n-button class="text-white" @click="handleClick"
                    :disabled="editPhoto.bg_removed_img || $page.props.auth.user.credit == 0" :loading="remvingBG">
                    {{ $t('home.RemoveBackground') }}
                </n-button>
            </div>
            <div class="w-full mt-3">
                <div class="flex justify-center mb-2">
                    <canvas class="border-2 border-solid" ref="canvasRef" width="500" height="500"></canvas>
                </div>
                <div class="flex justify-center">
                    <!-- <PrimaryButton class="mr-2 capitalize bg-blue-400" @click="handleActive(0)">
                        Select Background Layer
                    </PrimaryButton>
                    <PrimaryButton class="mr-2 capitalize bg-yellow-400" @click="handleActive(1)"
                        :disabled="!editPhoto.bg_removed_img">
                        Select Photo Layer
                    </PrimaryButton> -->
                    <PrimaryButton class="capitalize mr-2 flex items-center" @click="handleDownload()">
                        <n-icon class="mr-2">
                            <Download />
                        </n-icon>
                        {{ $t('common.Download') }}
                    </PrimaryButton>
                    <!-- <n-button class="capitalize bg-green-400 rounded" type="success" :lading="saving" @click="handleSave()">
                        <template #icon>
                            <n-icon>
                                <Save />
                            </n-icon>
                        </template>
                        Save
                    </n-button> -->
                </div>
            </div>
            <div class="w-full mt-3 flex justify-center">
                <n-upload abstract class="text-center" accept="image/png, image/jpeg, image/webp" :show-file-list="false"
                    :action="route('photo.bg.upload')" :data="{ ...exData, id: editPhoto.id }" @finish="handleSuccess"
                    @before-upload="handleBefore" @error="handleError">
                    <n-upload-trigger #="{ handleClick }" abstract>
                        <n-button :loading="loading" @click="handleClick">
                            <template #icon>
                                <n-icon>
                                    <CloudUploadAlt />
                                </n-icon>
                            </template>
                            {{ $t('home.UseCustomBG', {w: 512, h: 512}) }}
                        </n-button>
                    </n-upload-trigger>
                </n-upload>
            </div>
            <div class="w-full flex justify-around flex-wrap">
                <div v-if="editPhoto.bg_custom" class="w-1/3 sm:w-1/4 md:w-1/5 p-3" @click="handleBG(-1)">
                    <img :src="editPhoto.bg_custom" alt="bg" class="w-full"
                        :class="selectedBg == -1 ? 'border-solid border-4 border-rose-900' : ''">
                </div>
                <div v-else class="w-1/3 sm:w-1/4 md:w-1/5 p-3 cursor-pointer">
                    <n-upload abstract class="text-center" accept="image/png, image/jpeg, image/webp" :show-file-list="false"
                    :action="route('photo.bg.upload')" :data="{ ...exData, id: editPhoto.id }" @finish="handleSuccess"
                    @before-upload="handleBefore" @error="handleError">
                    <n-upload-trigger #="{ handleClick }" abstract>
                        <img src="/imgs/photo_bg/BG-custom.png" alt="bg" class="w-full" @click="handleClick">
                    </n-upload-trigger>
                </n-upload>
                </div>
                <div v-for="(item, index) in new Array(14)" class="w-1/3 sm:w-1/4 md:w-1/5 p-3" @click="handleBG(index)">
                    <img :src="`/imgs/photo_bg/BG-${index}.png`" alt="bg" class="w-full cursor-pointer"
                        :class="selectedBg == index ? 'border-solid border-4 border-rose-900' : ''">
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
