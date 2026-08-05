import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react(), tailwindcss()],
  resolve: {
    alias: {
      '@': import.meta.dirname + '/src',
      '@api': import.meta.dirname + '/src/api',
      '@components': import.meta.dirname + '/src/components',
      '@features': import.meta.dirname + '/src/features',
      '@hooks': import.meta.dirname + '/src/hooks',
      '@layouts': import.meta.dirname + '/src/layouts',
      '@pages': import.meta.dirname + '/src/pages',
      '@router': import.meta.dirname + '/src/router',
      '@services': import.meta.dirname + '/src/services',
      '@store': import.meta.dirname + '/src/store',
      '@styles': import.meta.dirname + '/src/styles',
      '@types': import.meta.dirname + '/src/types',
      '@utils': import.meta.dirname + '/src/utils',
      '@assets': import.meta.dirname + '/src/assets',
    },
  },
})
