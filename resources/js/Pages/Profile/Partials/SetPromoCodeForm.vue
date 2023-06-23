<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
});

const user = usePage().props.auth.user;

const form = useForm({
  promo_code: user.promo_code
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">{{ $t('profile.SetPromoCode') }}</h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ $t('profile.UpdatePromoTip') }}
            </p>
        </header>

        <form @submit.prevent="form.put(route('profile.promo.update'))" class="mt-6 space-y-6">
            <div>
                <InputLabel for="promo_code" :value="$t('profile.PromoCode')" />

                <TextInput
                    id="promo_code"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.promo_code"
                    placeholder="Promo Code"
                    required
                />

                <InputError class="mt-2" :message="form.errors.promo_code" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">{{ $t('common.Save') }}</PrimaryButton>

                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">{{ $t('common.Saved') }}.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
