import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  base: '/Proyecto-U5---FullStack/',
  plugins: [vue()],
  server: { port: 5173 }
})
