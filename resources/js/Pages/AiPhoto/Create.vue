<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { CloudUploadOutline } from '@vicons/ionicons5'
import { Image as faImage, Cogs, Cog } from '@vicons/fa'
import { toast } from 'vue3-toastify';
import { trans } from 'laravel-vue-i18n';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import * as tf from '@tensorflow/tfjs';

const props = defineProps({
    csrfToken: String,
    en_id: Number,
});

const form = useForm({
    type: 'man',
    eye: 'brown',
    retouch: '1',
});

const understand = ref(false);
const videoRef = ref(null);
const fileLists = ref([]);
const errors = ref(usePage().props.errors);
const optionsModal = ref(false);
let photoList = [];
const errPhotoList = [];
const exData = ref({
    _token: props.csrfToken
});
const options = computed({
    get() {
        return [
            // { label: 'Auto', value: 'person' },
            { label: trans('home.Man'), value: 'man' },
            { label: trans('home.Woman'), value: 'woman' },
            { label: trans('home.Boy'), value: 'boy' },
            { label: trans('home.Girl'), value: 'girl' },
        ]
    }
});
const options1 = computed({
    get() {
        return [
            // { label: 'Auto', value: 'person' },
            { label: trans('home.Brown'), value: 'brown' },
            { label: trans('home.Blue'), value: 'blue' },
            { label: trans('home.Green'), value: 'green' },
        ];
    }
});
const options2 = computed({
    get() {
        return [
            // { label: 'Auto', value: 'person' },
            { label: trans('home.On'), value: '1' },
            { label: trans('home.Off'), value: '0' },
        ];
    }
});

onMounted(() => {
    gtag('event', 'conversion', {
        'send_to': 'AW-10849410350/sr8jCNu0tZQYEK66s7Uo',
        'transaction_id': usePage().props.auth.user.id
    });
});

const beforeUpload = ({ file, fileList }) => {
    let text;
    for (const fi of fileList) {
        if (fi.file.size == file.file.size && fi.file.lastModified == file.file.lastModified) {
            text = trans('home.already');
            break;
        }
    }

    if (text) {
        toast.warning(text, {
            atoClose: 3000,
        });
        return false;
    }
    file.name = '';
}

const handleClick = () => {
    if (form.processing) return;

    if (fileLists.value.some((file) => (file.status == "uploading"))) return errors.value.message = trans('home.still');

    let uploadPhotos = photoList.filter(pho => !pho.filtered);

    if (uploadPhotos.length < 4 || uploadPhotos.length > 50) {
        return errors.value.message = trans('home.required', { min: 4, max: 50 });
    }

    if (!understand.value) {
        return errors.value.message = trans('home.check');
    }

    optionsModal.value = true;
}

const handleStart = () => {
    let uploadPhotos = photoList.filter(pho => !pho.filtered);
    form.transform(data => ({
        ...data,
        errPhotos: JSON.stringify(errPhotoList),
        photos: JSON.stringify(uploadPhotos.map(list => list.serverPath))
    })).post(props.en_id ? route('photo.enterprise.generate', props.en_id) : route('photo.generate'), {
        onFinish: (res) => {
            console.log('finished');
            optionsModal.value = false;
        }
    });
}

const handleChecked = (checked) => {
    errors.value.message = undefined;
}

const handleError = ({ file, event }) => {
    console.log(file)
    let errorMessage = JSON.parse(event.target.response).message;
    let path = JSON.parse(event.target.response).path;
    if (path) {
        errPhotoList.push(path);
    }
    file.name = errorMessage;
    if (!fileLists.value.some((file) => (file.status == "uploading"))) errors.value.message = undefined;
}

const handleSuccess = ({ file, event }) => {
    // Handle successful upload
    file.name = "";
    const fileId = file.id;
    const data = JSON.parse(event.target.response);
    const serverPath = data.path;
    // const embedding = data.embedding;
    // const embeddingTensor = tf.tensor(embedding).flatten();
    file.thumbnailUrl = `/storage/uploads/cropped/${serverPath}`;

    if (!fileLists.value.some((file) => (file.status == "uploading"))) errors.value.message = undefined;
    photoList.push({ fileId, serverPath });
    // photoList.push({ fileId, serverPath, embeddingTensor });
    // if(photoList.length > 1) {
    //     file.percentage = 99;
    //     file.status = 'uploading';
    // }
    // compareImages(embeddingTensor, file);
}

const compareImages = (tensor, file) => {
    let length = photoList.filter(pho => !pho.filtered).length;
    if (length < 2) return;
    length = photoList.length;
    let flag = true;
    for (let i = 0; i < length - 1; i++) {
        let photo = photoList[i];
        let similarityScore = calculateSimilarityScore(tensor, photo.embeddingTensor);

        if (similarityScore > 0.6) {
            photoList[i].filtered = true;
            errPhotoList.push(photo.serverPath);
            let ind = fileLists.value.findIndex(fi => fi.id == file.id);
            file.status = 'error';
            file.percentage = 100;
            file.name = trans('home.SimilaryError')
            fileLists.value[ind] = file;
            flag = false;
            break;
        }
    }
    if (flag) {
        let ind = fileLists.value.findIndex(fi => fi.id == file.id);
        file.status = 'finished';
        file.percentage = 100;
        fileLists.value[ind] = file;
    }
}

const handleRemove = ({ file }) => {
    if (file.status === "finished") {
        let deleteFile = photoList.filter(list => list.fileId == file.id)[0];
        photoList = photoList.filter(list => list.fileId != file.id);
        axios.post(route('photo.delete'), {
            ...exData.value,
            path: deleteFile.serverPath
        });
    }
}

const handleUpdate = (fileList) => {
    if (errors.value.message == trans('home.required', { min: 4, max: 50 }) && (fileList.length > 3 || fileList.length < 51)) {
        return errors.value.message = null;
    }
    if (errors.value.message === null && (fileList.length < 4 || fileList.length > 50)) {
        return errors.value.message = trans('home.required', { min: 4, max: 50 });
    }

    if (errors.value.message === undefined && (fileList.length > 50)) {
        return errors.value.message = trans('home.required', { min: 4, max: 50 });
    }
}

function calculateSimilarityScore(tensor1, tensor2) {

    const dotProduct = tf.dot(tensor1, tensor2);

    // Calculate the norms
    const norm1 = tf.norm(tensor1);
    const norm2 = tf.norm(tensor2);

    // Calculate the similarity score
    const similarityScore = dotProduct.div(norm1.mul(norm2)).dataSync()[0];
    // Do something with the similarity score
    return similarityScore;
    // Clean up
    // image1Tensor.dispose();
    // image2Tensor.dispose();
}


</script>

<template>
    <AuthenticatedLayout>

        <Head :title="`| ${$t('home.Home')}`" />

        <template #header>
            <div class="max-w-5xl mt-10 mx-auto text-center">
                <h2 class="text-xl md:text-3xl font-bold tracking-widest lg:text-5xl text-black whitespace-pre-wrap">
                    {{ $t('home.Title') }}
                </h2>
            </div>
        </template>
        <div class="max-w-5xl mx-auto flex justify-center pb-10 flex-wrap">
            <div class="w-full text-center">
                <video class="max-w-4xl w-[95%] md:w-[90%] mx-auto mb-[40px] px-5" ref="videoRef"
                    src="/videos/Headshot_Tips.mp4" controls loop autoplay poster="/imgs/tip_video_poster.png"></video>
                <hr />
            </div>
            <div class="w-full sm:w-1/2 md:w-1/3 flex py-5 sm:py-10 flex-col items-center">
                <img src="/imgs/gallery.png" alt="Gallery">
                <span class="text-center mt-2 whitespace-pre-wrap">
                    {{ $t('home.UploadTip1', { min: 7, max: 10 }) }}
                </span>
            </div>
            <div class="w-full sm:w-1/2 md:w-1/3 flex py-5 sm:py-10 flex-col items-center">
                <img src="/imgs/lamp.png" alt="Lamp">
                <span class="text-center mt-2 whitespace-pre-wrap">
                    {{ $t('home.UploadTip2') }}
                </span>
            </div>
            <div class="w-full sm:w-1/2 md:w-1/3 flex py-5 sm:py-10 flex-col items-center">
                <img src="/imgs/face_rec.png" alt="face_rec">
                <span class="text-center mt-2 whitespace-pre-wrap">
                    {{ $t('home.UploadTip3') }}
                </span>
            </div>
            <div class="w-full sm:w-1/2 md:w-1/3 flex py-5 sm:py-10 flex-col items-center">
                <img src="/imgs/crop.png" alt="crop">
                <span class="text-center mt-2 whitespace-pre-wrap">
                    {{ $t('home.UploadTip4') }}
                </span>
            </div>
            <div class="w-full sm:w-1/2 md:w-1/3 flex py-5 sm:py-10 flex-col items-center">
                <img src="/imgs/sunglass.png" alt="sunglass">
                <span class="text-center mt-2 whitespace-pre-wrap">
                    {{ $t('home.UploadTip5') }}
                </span>
            </div>
            <div class="w-full sm:w-1/2 md:w-1/3 flex py-5 sm:py-10 flex-col items-center">
                <img src="/imgs/angle.png" alt="angle">
                <span class="text-center mt-2 whitespace-pre-wrap">
                    {{ $t('home.UploadTip6') }}
                </span>
            </div>
            <div class="w-full mt-4 px-4">
                <n-upload accept="image/png, image/jpeg, image/webp" multiple v-model:file-list="fileLists" directory-dnd
                    :max="50" :action="route('photo.upload')" list-type="image" @before-upload="beforeUpload" :data="exData"
                    @finish="handleSuccess" @remove="handleRemove" @error="handleError" @update:file-list="handleUpdate">
                    <n-upload-dragger abstract class="bg-gray-100 py-16">
                        <div class="mb-3 text-center">
                            <n-icon class="text-6xl text-primary">
                                <CloudUploadOutline />
                            </n-icon>

                            <button
                                class="border-0  cursor-pointer mx-auto rounded font-bold border mt-4 flex items-center bg-white text-primary px-12 py-3 shadow">
                                {{ $t('home.SelectImages') }}
                                <n-icon class="ml-2 text-2xl text-primary">
                                    <faImage />
                                </n-icon>
                            </button>
                        </div>
                        <n-text class="font-bold text-gray-400">
                            {{ $t('home.SelectTip') }}
                        </n-text>
                    </n-upload-dragger>
                    <!-- <n-upload-file-list list-type="image" /> -->
                </n-upload>
                <div class="text-center px-5">
                    <n-checkbox class="mt-6" v-model:checked="understand" @update:checked="handleChecked">
                        <span class="font-bold">{{ $t('home.SelectWarnning') }}</span>
                    </n-checkbox>
                    <br />
                    <n-alert class="max-w-4xl mx-auto mt-5" type="error" closable v-if="errors.message">
                        {{ errors.message }}
                    </n-alert>
                    <br />
                    <PrimaryButton @click="handleClick" class="flex items-center mt-6 px-16 py-4"
                        :disabled="form.processing || errors.message">
                        <n-icon v-if="form.processing" class="text-xl">
                            <Cog class="animate-spin" />
                        </n-icon>
                        <n-icon v-else class="text-xl">
                            <Cogs />
                        </n-icon>
                        <span class="ml-2">{{ $t('common.Generate') }}</span>
                    </PrimaryButton>
                    <div class="text-center mt-4">
                        <small class="text-gray-400">
                            {{ $t('home.UploadWarning') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <n-modal v-model:show="optionsModal" class="custom-card" preset="card" :style="{ width: '450px' }"
            title="Please select" :bordered="false" :segmented="{
                content: 'soft',
                footer: 'soft'
            }">
            <div class="">
                <h3 class="m-0 mb-1 text-gray-600">{{ $t('home.SelectType') }}</h3>
                <n-radio-group v-model:value="form.type">
                    <n-radio-button v-for="(item, index) in options" :key="index" :value="item.value">{{ item.label
                    }}</n-radio-button>
                </n-radio-group>
                <h3 class="mb-1 text-gray-600">{{ $t('home.SelectEyeColor') }}</h3>
                <n-radio-group v-model:value="form.eye">
                    <n-radio-button v-for="(item, index) in options1" :key="index" :value="item.value">{{ item.label
                    }}</n-radio-button>
                </n-radio-group>
                <h3 class="mb-1 text-gray-600">{{ $t('home.PhotoRetouchingTip') }}</h3>
                <n-radio-group v-model:value="form.retouch">
                    <n-radio-button v-for="(item, index) in options2" :key="index" :value="item.value">{{ item.label
                    }}</n-radio-button>
                </n-radio-group>
                <br>
                <small>
                    {{ $t('home.PhotoRetouchingWarning') }}
                </small>
            </div>
            <template #footer>
                <div class="flex justify-end">
                    <n-button class="mr-2" type="error" @click="optionsModal = false">{{ $t('common.Close') }}</n-button>
                    <n-button :loading="form.processing" @click="handleStart">{{ $t('common.Start') }}</n-button>
                </div>
            </template>
        </n-modal>
    </AuthenticatedLayout>
</template>
