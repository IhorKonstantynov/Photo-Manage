<script setup>
  defineProps({
    loading: {
      type: Boolean,
      default: false
    }
  })
</script>

<template>
  <div class="text-center absolute z-10 top-6 left-1/2 -translate-x-1/2">
    <img src="/imgs/BiggerLogo.png" alt="logo" class="animate-pulse max-w-[60px] mx-auto" />
    <n-h2 class="animate-pulse text-primary mt-0">Loading...</n-h2>
  </div>
</template>