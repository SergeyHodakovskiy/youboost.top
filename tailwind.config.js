// tailwind.config.js
export default {
  content: [
    './templates/**/*.html.twig',
    './assets/**/*.js',
  ],
  daisyui: {
    themes: ['light', 'dark', 'cupcake'], // Укажи нужные темы
  },
  theme: {
    extend: {
      colors: {
        primary: '#007BFF', // Основной синий
        danger: '#DC3545',  // Красный для акцентов
        warning: '#FFA500', // Оранжевый
      },
    },
  },
  plugins: [require("daisyui")],
  daisyui: {
    themes: [
      {
        light: {
          "primary": "oklch(45% 0.24 277.023)",
          "primary-content": "oklch(93% 0.034 272.788)",
          "secondary": "oklch(65% 0.241 354.308)",
          "secondary-content": "oklch(94% 0.028 342.258)",
          "accent": "oklch(77% 0.152 181.912)",
          "accent-content": "oklch(38% 0.063 188.416)",
          "neutral": "oklch(13% 0.028 261.692)",
          "neutral-content": "oklch(87% 0.01 258.338)",
          "base-100": "oklch(100% 0 0)",
          "base-200": "oklch(98% 0 0)",
          "base-300": "oklch(95% 0 0)",
          "base-content": "oklch(27% 0.006 286.033)",
          "info": "oklch(74% 0.16 232.661)",
          "success": "oklch(76% 0.177 163.223)",
          "warning": "oklch(82% 0.189 84.429)",
          "error": "oklch(71% 0.194 13.428)",
        },
        dark: {
          "primary": "oklch(58% 0.233 277.117)",
          "primary-content": "oklch(96% 0.018 272.314)",
          "secondary": "oklch(65% 0.241 354.308)",
          "secondary-content": "oklch(94% 0.028 342.258)",
          "accent": "oklch(77% 0.152 181.912)",
          "accent-content": "oklch(38% 0.063 188.416)",
          "neutral": "oklch(14% 0.005 285.823)",
          "neutral-content": "oklch(92% 0.004 286.32)",
          "base-100": "oklch(25.33% 0.016 252.42)",
          "base-200": "oklch(23.26% 0.014 253.1)",
          "base-300": "oklch(21.15% 0.012 254.09)",
          "base-content": "oklch(97.807% 0.029 256.847)",
          "info": "oklch(74% 0.16 232.661)",
          "success": "oklch(76% 0.177 163.223)",
          "warning": "oklch(82% 0.189 84.429)",
          "error": "oklch(71% 0.194 13.428)",
        }
      }
    ],
  },
};