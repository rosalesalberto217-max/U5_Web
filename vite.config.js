import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  base: '/U5_Web/',
  plugins: [vue()],
  server: { port: 5173 }
})
