import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig(({ mode }) => ({
  plugins: [
    tailwindcss(),
    laravel({
      input: [
        'resources/css/app.css',
        'resources/css/catalogos.css',
        'resources/css/formulario.css',
        'resources/css/login.css',
        'resources/css/sincronizadores.css',
        'resources/css/style.css',
        'resources/css/tablas.css',
        'resources/css/variables.css',
        'resources/js/app.js',
        'resources/js/bootstrap.js',
        'resources/js/cotizaciones.js',
        'resources/js/validaciones.js',
      ],
      refresh: true,
    }),
  ],

  build: {
    target: 'es2018',
    cssMinify: true,
    sourcemap: false,
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: true,
        drop_debugger: true,
        passes: 2,
      },
      format: {
        comments: false,
      },
    },
    rollupOptions: {
      output: {
        manualChunks: undefined,
      },
    },
  },

  server: {
    cors: true,
    hmr: { host: 'localhost' },
  },
}))

