<template>
  <div class="flex min-h-screen bg-slate-950">
    <!-- Mobile Overlay -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden"
      @click="sidebarOpen = false"
    ></div>

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed lg:static inset-y-0 left-0 z-50 w-64 flex flex-col',
        'bg-slate-900/80 backdrop-blur-xl border-r border-white/10',
        'transform transition-transform duration-300 ease-in-out',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
      ]"
    >
      <!-- Logo -->
      <div class="p-6 border-b border-white/5">
        <router-link to="/dashboard" class="flex items-center gap-3" @click="sidebarOpen = false">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/30">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
            </svg>
          </div>
          <span class="text-xl font-extrabold gradient-text-bright">ContactOS</span>
        </router-link>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 p-4 space-y-1">
        <router-link
          v-for="item in navItems"
          :key="item.path"
          :to="item.path"
          @click="sidebarOpen = false"
          :class="[
            'flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200',
            isActive(item.path)
              ? 'bg-gradient-to-r from-indigo-500/20 to-violet-500/20 text-white border border-indigo-500/20 shadow-lg shadow-indigo-500/5'
              : 'text-slate-400 hover:text-white hover:bg-white/5'
          ]"
        >
          <div v-html="item.icon" class="w-5 h-5 flex-shrink-0"></div>
          {{ item.label }}
        </router-link>
      </nav>

      <!-- User Section -->
      <div class="p-4 border-t border-white/5">
        <div class="flex items-center gap-3 px-3 py-2">
          <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0 border-2 border-indigo-500/30">
            <img
              v-if="authStore.userPhoto"
              :src="authStore.userPhoto"
              class="w-full h-full object-cover"
              alt="Avatar"
            />
            <div v-else class="w-full h-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center">
              <span class="text-sm font-bold text-white">{{ userInitial }}</span>
            </div>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-white truncate">{{ authStore.user?.nombre_de_usuario }}</p>
            <p class="text-xs text-slate-500">En línea</p>
          </div>
          <button
            @click="handleLogout"
            class="p-2 rounded-lg hover:bg-rose-500/10 text-slate-400 hover:text-rose-400 transition-all duration-200"
            title="Cerrar sesión"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
            </svg>
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen">
      <!-- Mobile Header -->
      <header class="lg:hidden sticky top-0 z-30 flex items-center gap-4 px-4 py-3 bg-slate-900/80 backdrop-blur-xl border-b border-white/10">
        <button
          @click="sidebarOpen = true"
          class="p-2 rounded-lg hover:bg-white/5 text-slate-400 hover:text-white transition-colors"
        >
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
        <span class="text-lg font-bold gradient-text-bright">ContactOS</span>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-4 md:p-8 overflow-y-auto">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const sidebarOpen = ref(false)

const userInitial = computed(() => {
  const name = authStore.user?.nombre_de_usuario || '?'
  return name.charAt(0).toUpperCase()
})

const navItems = [
  {
    path: '/dashboard',
    label: 'Dashboard',
    icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>'
  },
  {
    path: '/contacts/new',
    label: 'Nuevo Contacto',
    icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" /></svg>'
  },
  {
    path: '/profile',
    label: 'Mi Perfil',
    icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" /></svg>'
  }
]

function isActive(path) {
  if (path === '/dashboard') return route.path === '/dashboard'
  return route.path.startsWith(path)
}

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>
