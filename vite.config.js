import { defineConfig } from 'vite';
import symfonyPlugin from 'vite-plugin-symfony';
import tailwindcss from '@tailwindcss/postcss';

export default defineConfig({
  plugins: [
    symfonyPlugin({
      phpBinary: '/usr/bin/php', // Explicitly set PHP path
    }),
  ],
  css: {
    postcss: {
      plugins: [
        tailwindcss(),
      ],
    },
  },
  build: {
    rollupOptions: {
      input: {
        app: './assets/app.js',
      },
    },
  },
  // server: {
  //   cors: {
  //     origin: ['http://youboost.loc', 'https://localhost:8000', 'https://localhost:8001'], // Разрешить запросы с этого домена
  //     methods: 'GET,POST,PUT,DELETE', // Разрешенные HTTP-методы
  //     credentials: true, // Разрешить передачу куки и заголовков авторизации
  //   },
  // },
});