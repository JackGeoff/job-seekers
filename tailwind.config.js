/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        // Primary brand color - modern indigo
        primary: {
          50: '#f0f4ff',
          100: '#e0e9fe',
          200: '#c7d2fe',
          300: '#a5b4fc',
          400: '#818cf8',
          500: '#6366f1',
          600: '#4f46e5',
          700: '#4338ca',
          800: '#3730a3',
          900: '#312e81',
        },
        // Neutral colors for clean, modern feel
        neutral: {
          50: '#fafafa',
          100: '#f5f5f5',
          200: '#e5e5e5',
          300: '#d4d4d4',
          400: '#a3a3a3',
          500: '#737373',
          600: '#525252',
          700: '#404040',
          800: '#262626',
          900: '#171717',
        },
        // Semantic colors
        success: {
          50: '#f0fdf4',
          500: '#22c55e',
          600: '#16a34a',
        },
        error: {
          50: '#fef2f2',
          500: '#ef4444',
          600: '#dc2626',
        },
        warning: {
          50: '#fffbeb',
          500: '#f59e0b',
          600: '#d97706',
        },
        info: {
          50: '#f0f9ff',
          500: '#06b6d4',
          600: '#0891b2',
        },
      },
      typography: {
        DEFAULT: {
          css: {
            '--tw-prose-body': 'rgb(107, 114, 128)',
            '--tw-prose-headings': 'rgb(17, 24, 39)',
          },
        },
      },
      fontFamily: {
        sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      fontSize: {
        // Modern typography scale
        xs: ['0.75rem', { lineHeight: '1rem' }],
        sm: ['0.875rem', { lineHeight: '1.25rem' }],
        base: ['1rem', { lineHeight: '1.5rem' }],
        lg: ['1.125rem', { lineHeight: '1.75rem' }],
        xl: ['1.25rem', { lineHeight: '1.75rem' }],
        '2xl': ['1.5rem', { lineHeight: '2rem' }],
        '3xl': ['1.875rem', { lineHeight: '2.25rem' }],
        '4xl': ['2.25rem', { lineHeight: '2.5rem' }],
        '5xl': ['3rem', { lineHeight: '1.2' }],
        '6xl': ['3.75rem', { lineHeight: '1.2' }],
      },
      spacing: {
        // Generous spacing for modern feel
        0: '0',
        1: '0.25rem',
        2: '0.5rem',
        3: '0.75rem',
        4: '1rem',
        5: '1.25rem',
        6: '1.5rem',
        8: '2rem',
        10: '2.5rem',
        12: '3rem',
        14: '3.5rem',
        16: '4rem',
        20: '5rem',
        24: '6rem',
        28: '7rem',
        32: '8rem',
        40: '10rem',
      },
      borderRadius: {
        // Not excessively rounded, subtle curves
        none: '0',
        sm: '0.375rem',
        DEFAULT: '0.5rem',
        md: '0.625rem',
        lg: '0.75rem',
        xl: '1rem',
        '2xl': '1.25rem',
        '3xl': '1.5rem',
        full: '9999px',
      },
      boxShadow: {
        // Subtle, sophisticated shadows
        'none': 'none',
        'sm': '0 1px 2px 0 rgb(0 0 0 / 0.05)',
        'base': '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)',
        'md': '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
        'lg': '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)',
        'xl': '0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)',
        'focus': '0 0 0 3px rgb(99, 102, 241 / 0.1), 0 0 0 1px rgb(99, 102, 241 / 0.5)',
      },
      animation: {
        // Smooth, subtle animations
        'fade-in': 'fadeIn 0.3s ease-in-out',
        'fade-out': 'fadeOut 0.3s ease-in-out',
        'slide-up': 'slideUp 0.4s ease-out',
        'slide-down': 'slideDown 0.4s ease-out',
        'pulse-subtle': 'pulseSubtle 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        fadeOut: {
          '0%': { opacity: '1' },
          '100%': { opacity: '0' },
        },
        slideUp: {
          '0%': { transform: 'translateY(10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        slideDown: {
          '0%': { transform: 'translateY(-10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        pulseSubtle: {
          '0%, 100%': { opacity: '1' },
          '50%': { opacity: '0.8' },
        },
      },
      transitionTimingFunction: {
        'smooth': 'cubic-bezier(0.4, 0, 0.2, 1)',
      },
    },
  },
  plugins: [
    // Custom component layer utilities
    ({ addComponents, theme }) => {
      addComponents({
        // Modern button styles
        '.btn': {
          '@apply inline-flex items-center justify-center px-6 py-3 rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 cursor-pointer text-base': '',
        },
        '.btn-primary': {
          '@apply btn bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500 shadow-sm hover:shadow-md': '',
        },
        '.btn-secondary': {
          '@apply btn bg-neutral-100 text-neutral-900 hover:bg-neutral-200 focus:ring-neutral-500 border border-neutral-200': '',
        },
        '.btn-ghost': {
          '@apply btn text-primary-600 hover:bg-primary-50 focus:ring-primary-500': '',
        },
        '.btn-sm': {
          '@apply px-4 py-2 text-sm': '',
        },
        '.btn-lg': {
          '@apply px-8 py-4 text-lg': '',
        },

        // Modern input styles
        '.input': {
          '@apply w-full px-4 py-3 rounded-lg bg-white border border-neutral-200 text-neutral-900 placeholder-neutral-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent': '',
        },
        '.input-sm': {
          '@apply px-3 py-2 text-sm': '',
        },
        '.input-lg': {
          '@apply px-5 py-4 text-lg': '',
        },

        // Modern form labels
        '.label': {
          '@apply block text-sm font-medium text-neutral-700 mb-2': '',
        },

        // Form group wrapper
        '.form-group': {
          '@apply mb-6': '',
        },

        // Modern card
        '.card': {
          '@apply bg-white rounded-xl border border-neutral-200 shadow-sm p-6': '',
        },
        '.card-hover': {
          '@apply card transition-all duration-300 hover:shadow-md hover:border-neutral-300': '',
        },

        // Section divider
        '.divider': {
          '@apply border-t border-neutral-200': '',
        },

        // Text utilities
        '.text-muted': {
          '@apply text-neutral-500': '',
        },
        '.text-emphasis': {
          '@apply text-neutral-900 font-medium': '',
        },
      });
    },
  ],
};
