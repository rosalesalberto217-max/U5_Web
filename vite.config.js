import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  base: '/Proyecto-Unidad-5---Aplicaci-n-Web-Full-Stack-con-Vue-3-PHP-y-MySQL-Frontend-/',
  plugins: [vue()],
  server: { port: 5173 }
})
