import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'
import laravel from 'laravel-vite-plugin'
import path from 'path'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    laravel({
      input: 'resources/js/main.tsx',
      refresh: true,
    }),
    react(),
    tailwindcss(),
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/js'),
      '@api': path.resolve(__dirname, 'resources/js/api'),
      '@components': path.resolve(__dirname, 'resources/js/components'),
      '@features': path.resolve(__dirname, 'resources/js/features'),
      '@hooks': path.resolve(__dirname, 'resources/js/hooks'),
      '@layouts': path.resolve(__dirname, 'resources/js/layouts'),
      '@pages': path.resolve(__dirname, 'resources/js/pages'),
      '@router': path.resolve(__dirname, 'resources/js/router'),
      '@services': path.resolve(__dirname, 'resources/js/services'),
      '@store': path.resolve(__dirname, 'resources/js/store'),
      '@styles': path.resolve(__dirname, 'resources/js/styles'),
      '@types': path.resolve(__dirname, 'resources/js/types'),
      '@utils': path.resolve(__dirname, 'resources/js/utils'),
      '@assets': path.resolve(__dirname, 'resources/js/assets'),
    },
  },
})
