<script setup>

import Sidebar from '@/components/layout/Sidebar.vue'

import Navbar from '@/components/layout/Navbar.vue'

</script>

<template>

<div class="flex min-h-screen bg-slate-900">

    <Sidebar />

    <div class="flex-1">

        <Navbar />

        <main class="p-6">

            <slot />

        </main>

    </div>

</div>

</template>