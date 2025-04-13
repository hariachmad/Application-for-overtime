import { defineConfig } from 'vite'
import tailwindcss from 'tailwindcss'
import autoprefixer from 'autoprefixer'

export default defineConfig({
  build: {
    outDir: 'dist',
    rollupOptions: {
      input: 'input.css',
      output: {
        assetFileNames: 'output.css'
      }
    }
  },
  css: {
    postcss: {
      plugins: [tailwindcss, autoprefixer],
    }
  }
})