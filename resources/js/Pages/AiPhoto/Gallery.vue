<script setup>
import { ref, onMounted } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import moment from "moment";
import { Head, usePage } from "@inertiajs/vue3";
import { saveAs } from "file-saver";
import { Download } from "@vicons/carbon";
import { EyeRegular } from "@vicons/fa";
import Upgrade from "@/Pages/AiPhoto/Partials/Upgrade.vue";

const props = defineProps({
    result: {
        type: Array,
        default: [],
    },
    id: Number,
    type: String,
    csrfToken: String,
    canAdd: Boolean,
    isMobile: Boolean,
    message: String,
    highRes: Boolean,
});

const imageRef = ref(null);
const selectedImg = ref([]);
const exData = ref({
    _token: props.csrfToken,
});

const handleDownload = (url) => {
    if (props.isMobile) {
        if (props.type != "result") {
            if (props.type == "downloads") {
                url = `https://dashboard.prophotos.ai/${url}`;
            } else if (props.type == "cropped") {
            } else {
                url = `https://dashboard.prophotos.ai/storage/uploads/${props.type}/${url}`;
            }
        } else {
            axios
                .put(route("photos.downloaded"), {
                    ...exData.value,
                    id: props.id,
                    url,
                })
                .then(() => {
                    console.log("success");
                });
        }
        window.open(`${url}?highRes=${Number(props.highRes)}`, "_blank");
        return;
    }
    if (props.type != "result") {
        if (props.type == "downloads") {
            url = `https://dashboard.prophotos.ai/${url}`;
        } else if (props.type == "cropped") {
        } else {
            url = `https://dashboard.prophotos.ai/storage/uploads/${props.type}/${url}`;
        }
        return saveAs(
            url,
            `photo_${moment().format("YYYY-MM-DD_HH:mm:ss")}.jpg`
        );
    }
    axios
        .put(route("photos.downloaded"), {
            ...exData.value,
            id: props.id,
            url,
        })
        .then(() => {
            console.log("success");
        });
    // axios.get(`http://localhost:3000/get-image?image_url=${url}`, {
    axios
        .get(`${url}?highRes=${Number(props.highRes)}`, {
            responseType: "blob",
        })
        .then(function (response) {
            const image = URL.createObjectURL(response.data);
            saveAs(
                image,
                `my-photo_${moment().format("YYYY-MM-DD_HH:mm:ss")}.jpg`
            );
        })
        .catch(function (error) {
            console.error(error);
        });
};
onMounted(() => {});

const handleClick = (i) => {
    imageRef.value[i].click();
};

const handleChange = (e, img) => {
    selectedImg.value = selectedImg.value.filter((selImg) => selImg != img);
    if (e && selectedImg.value.length < 2) {
        selectedImg.value.push(img);
    }
};

// const handleEdit = (img) => {
//     console.log(img);
// }
</script>

<template>
    <Head :title="`| ${$t('home.GalleryImages')}`" />
    <AuthenticatedLayout>
        <template #header>
            <Upgrade
                v-if="type == 'result'"
                :message="message"
                :id="id"
                :canAdd="canAdd"
                :highRes="highRes"
            />
            <div
                class="max-w-6xl mx-auto text-center bg-white pb-10 pt-5 px-2 sm:px-6 lg:px-8"
            >
                <h2
                    v-if="type == 'result'"
                    class="text-sm sm:text-md lg:text-lg font-bold text-gray-600 leading-5 md:leading-4 whitespace-pre-wrap"
                    v-html="$t('home.AIResultTip')"
                >
                </h2>
                <h2
                    v-else
                    class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-600 leading-5 md:leading-4 capitalize"
                >
                    {{ $t(`home.${type}`) }} {{ $t('home.Images') }}
                </h2>
            </div>
        </template>
        <div>
            <div class="max-w-6xl mx-auto pb-10 px-2 sm:px-6 lg:px-8">
                <n-image-group>
                    <div
                        class="inline-block relative w-1/2 sm:w-1/3 md:w-1/4 lg:w-1/5 p-2"
                        v-for="[i, img] of result.entries()"
                        :key="i"
                    >
                        <n-image
                            ref="imageRef"
                            class="rounded"
                            :src="
                                type == 'result' || type == 'cropped'
                                    ? img
                                    : type == 'downloads'
                                    ? img.includes('server.prophotos.ai')
                                        ? img
                                        : `/${img}`
                                    : `/storage/uploads/${type}/${img}`
                            "
                        />
                        <div
                            class="absolute top-0 left-0 w-full h-full p-2 cursor-pointer pb-[14px]"
                        >
                            <div
                                class="w-full h-full flex items-center justify-center opacity-0 hover:opacity-100 bg-[#0004] rounded"
                            >
                                <n-button
                                    title="View Photo."
                                    class="bg-white text-black mr-2"
                                    type="primary"
                                    strong
                                    circle
                                    @click="handleClick(i)"
                                >
                                    <n-icon>
                                        <EyeRegular />
                                    </n-icon>
                                </n-button>
                                <!-- <Link :href="route('photo.edit', {id, index: i})" v-if="type == 'result'">
                                <n-button title="Remove background." class="bg-white text-black mr-2" type="primary" strong circle
                                    @click="handleEdit(img)">
                                    <n-icon>
                                        <ImageEdit24Regular />
                                    </n-icon>
                                </n-button>
                                </Link> -->
                                <n-button
                                    title="Download Photo."
                                    class="bg-white text-black"
                                    type="primary"
                                    strong
                                    circle
                                    @click="handleDownload(img)"
                                >
                                    <n-icon>
                                        <Download />
                                    </n-icon>
                                </n-button>
                            </div>
                        </div>
                        <div
                            v-if="$page.props.auth.user.permission == 1"
                            class="absolute top-0 right-0 w-16 h-16 p-2 flex justify-center items-center"
                        >
                            <n-checkbox
                                @update:checked="(e) => handleChange(e, img)"
                            >
                            </n-checkbox>
                        </div>
                    </div>
                </n-image-group>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
