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
        'text-fg-brand-strong',
        'bg-brand-softer',
        'rounded-base',
        'sm:items-center',

        // Clases de colores y fondos
        'bg-yellow-600',
        'border-yellow-800',
        'bg-white',
        'bg-red-100',
        'bg-green-100',
        'bg-yellow-100',
        'bg-blue-100',
        'bg-gray-100',
        'bg-gray-50',
        'bg-black',
        'bg-red-400',
        'bg-red-600',
        'bg-slate-100',
        'bg-slate-200',
        'bg-blue-50',
        'bg-blue-200', 'bg-blue-400', 'bg-blue-500',
        'bg-green-200', 'bg-green-400', 'bg-green-500',
        'bg-purple-200', 'bg-purple-400', 'bg-purple-500',
        'bg-yellow-200', 'bg-yellow-400', 'bg-yellow-500',
        'bg-red-200', 'bg-red-400', 'bg-red-500',
        'bg-indigo-200', 'bg-indigo-400', 'bg-indigo-500',
        'bg-pink-200', 'bg-pink-400', 'bg-pink-500',
        'bg-teal-200', 'bg-teal-400', 'bg-teal-500',
        'bg-orange-200', 'bg-orange-400', 'bg-orange-500',
        'bg-cyan-200', 'bg-cyan-400', 'bg-cyan-500',
        'bg-lime-200', 'bg-lime-400', 'bg-lime-500',
        'bg-amber-200', 'bg-amber-400', 'bg-amber-500',
        'bg-emerald-200', 'bg-emerald-400', 'bg-emerald-500',
        'bg-violet-200', 'bg-violet-400', 'bg-violet-500',
        'bg-fuchsia-200', 'bg-fuchsia-400', 'bg-fuchsia-500',

        // Clases de texto
        'text-black',
        'text-white',
        'text-red-500',
        'text-gray-500',
        'text-blue-500',
        'text-green-500',
        'text-yellow-500',
        'text-gray-800',
        'text-blue-800',
        'text-base',
        'text-xs',
        'text-sm',
        'text-md',
        'text-lg',
        'text-xl',
        'text-[12px]',
        'text-[#242e68]',

        // Clases de borde
        'border-2',
        'border-red-500',
        'border-blue-500',
        'border-l',
        'border-gray-200',
        'border-white',

        // Clases de espaciado
        'p-1',
        'p-2',
        'p-3',
        'p-4',
        'px-1',
        'px-2',
        'px-3',
        'px-4',
        'py-1',
        'py-2',
        'py-3',
        'py-4',
        'm-1',
        'm-2',
        'm-3',
        'm-4',
        'mb-1',
        'mb-2',
        'mb-4',
        'mb-10',
        'ml-2',
        'ml-6',
        'me-2',
        'mt-1',
        'mt-1.5',
        'pl-5',
        'space-y-1',
        'space-x-2',

        // Clases de flexbox
        'flex',
        'flex-wrap',
        'items-center',
        'items-start',
        'justify-center',
        'justify-between',
        'items-baseline',
        'flex-col',
        'justify-end',
        'self-start',

        // Clases de tamaño
        'w-full',
        'w-7',
        'h-7',
        'w-[80px]',
        'w-3',
        'h-3',
        'w-4',
        'h-4',
        'w-5',
        'h-5',

        // Clases de tipografía
        'font-bold',
        'font-medium',
        'font-normal',
        'font-semibold',
        'italic',

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
        'span-cyan',
        'span-purple',
        'span-orange',
        'span-indigo',

        // Clases de grid
        'grid',
        'grid-cols-3',
        'md:col-span-2',
        'col-span-1',
        'gap-2',

        // Clases de interacción
        'outline-none',
        'hover:bg-red-600',
        'hover:text-red-700',
        'cursor-pointer',
        'remove-entregable',

        // Clases de estado (ng)
        'ng-untouched',
        'ng-pristine',
        'ng-valid',

        // Clases de listas
        'list-disc',

        // Clases de visibilidad
        'hidden',

        // Otras clases útiles detectadas
        'max-w-4xl',
        'mx-auto',
        'group',
        'absolute',
        '-left-1.5',
        'flex-1',
        'w-auto',
        'h-full',
        'object-contain'
    ],

};
