<script setup>
import { ref, watch } from 'vue';
import { NRadioGroup, NRadio } from 'naive-ui';

const props = defineProps({
  options: {
    type: Array,
    required: true
  },
  value: {
    type: String,
    default: 'type',
  }
});

const selectedValue = ref(window.localStorage.getItem(props.value) ?? props.options[0].value);

watch(() => selectedValue.value, () => {
  window.localStorage.setItem(props.value, selectedValue.value);
})

</script>

<template>
  <n-radio-group v-model:value="selectedValue">
    <n-radio v-for="(item, index) in options" :key="index" :value="item.value">{{ item.label }}</n-radio>
  </n-radio-group>
</template>
