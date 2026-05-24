<template>
  <div class="min-h-screen bg-slate-950">
    <ToastNotification />
    <router-view v-slot="{ Component, route }">
      <transition name="page" mode="out-in">
        <component :is="Component" :key="route.path" />
      </transition>
    </router-view>
  </div>
</template>

<script setup>
import ToastNotification from './components/ToastNotification.vue'
</script>