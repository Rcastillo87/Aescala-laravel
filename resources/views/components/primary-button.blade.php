<button {{ $attributes->merge([
    'type' => 'submit', 
    'class' => 'inline-flex items-center px-4 py-2 bg-blue-800 dark:bg-blue-200 border border-transparent rounded-md font-semibold text-xs 
    text-white dark:text-blue-800 uppercase tracking-widest hover:text-blue-800 border-2 border-blue-800 hover:bg-white dark:hover:bg-white focus:bg-blue-700 dark:focus:bg-white 
     dark:active:bg-blue-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-blue-800 focus:text-white
    transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
