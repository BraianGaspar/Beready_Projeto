<template>
  <div>
    <router-view />
    <AlertContainer />
  </div>
</template>

<script setup lang="ts">
import AlertContainer from '@/components/common/AlertContainer.vue'
</script>
