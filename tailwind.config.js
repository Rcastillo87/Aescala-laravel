import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './node_modules/flowbite/**/*.js', // Asegura que Flowbite sea procesado
    ],
    
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms,
        require('flowbite/plugin'),    // Si estás usando Flowbite
    ],

    safelist: [
      // Clases de colores y fondos
      'bg-white',
      'bg-red-100',
      'bg-green-100',
      'bg-yellow-100',
      'bg-blue-100',
      'bg-gray-100',
      'bg-black',
      'bg-red-400',
      'bg-red-600',
      'bg-slate-100',
      'bg-slate-200',
      
      // Clases de texto
      'text-black',
      'text-white',
      'text-red-500',
      'text-gray-500',
      'text-blue-500',
      'text-green-500',
      'text-yellow-500',
      'text-gray-800',
      'text-red-500',
      
      // Clases de borde
      'border-2',
      'border-red-500',
      'border-blue-500',
      
      // Clases de espaciado
      'px-2',
      'px-4',
      'py-1',
      'py-2',
      'm-1',
      'ml-2',
      'space-y-1',
      'space-x-2',
      
      // Clases de flexbox
      'flex',
      'items-center',
      'justify-center',
      
      // Clases de tamaño
      'w-full',
      'w-7',
      'h-7',
      'w-[80px]',
      
      // Clases de tipografía
      'text-md',
      'text-sm',
      'text-[12px]',
      'font-bold',
      'font-medium',
      
      // Clases de forma
      'rounded',
      'rounded-md',
      'rounded-lg',
      'rounded-xl',
      'rounded-full',
      
      // Clases de gradiente
      'bg-gradient-to-tr',
      'bg-gradient-to-r',
      'from-red-600',
      'to-red-400',
      'from-slate-200',
      'to-slate-100',
      
      // Clases de estado
      'span-blue',
      'span-green',
      'span-yellow',
      'span-red',
      'span-gray',
      'span-black',
      
      // Clases de grid
      'md:col-span-2',
      'col-span-1',
      
      // Clases de interacción
      'outline-none',
      
      // Clases de estado (ng)
      'ng-untouched',
      'ng-pristine',
      'ng-valid',
      
      // Clases de visibilidad
      'hidden'
    ],

};
