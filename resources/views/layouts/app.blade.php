<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" sizes="16x16"  href="{{asset('favicon.png')}}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="{{asset('css/custom.css')}}" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
    </head>
    <body class="font-sans antialiased">
        <div class="flex flex-col min-h-screen">
            @include('layouts.sidebar')
            @include('layouts.navigation')
            <!-- Page Content -->
            <main class="sm:ml-64 ml-0 sm:mt-6 mt-2 sm:mb-16 mb-1 sm:mr-5 mx-2 p-4 rounded-2xl bg-white border-2 shadow-lg shadow-gray-300 overflow-hidden">                <x-title-component title="{{$title??''}}" />
                @yield('content')
            </main>
            @include('layouts.footer')
        </div>


        <div class="bg-white px-2 py-1 border-2 m-1 space-y-1 border-blue-500 rounded-xl hidden">
            <div class="flex items-center">
                <p class="text-md font-bold text-gray-500">Material: <p class="ml-2 text-black">taladro</p></p>
            </div>
            <div class="flex items-center space-x-2">
                <button class="border-2 border-red-500 rounded-md" type="button">
                    <svg class="w-7 h-7 text-red-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/>
                      </svg>
                </button>
                <div class="flex items-center justify-center text-[12px] text-center font-bold rounded h-7 w-[80px] text-white bg-gradient-to-tr from-red-600 to-red-400">
                    Und: 262
                </div>
                <input type="number" placeholder="Cantidad" id="input-proyecto" 
                class="w-full text-sm py-1 px-4 rounded-lg border outline-none ng-untouched ng-pristine ng-valid">
            </div>
            <p class="text-md font-bold text-gray-500">Observación : <small class="ml-2 text-black">MERCURI</small></p>
            <div class="bg-gradient-to-r from-slate-200 to-slate-100 rounded p-1 w-full">
                <p class="font-medium">$3,876 * 0 = <b class="text-red-500">$0</b></p>
            </div>
        </div>


        <div class="hidden">
            span-blue
            span-green
            span-yellow
            span-red
            span-gray
            span-black
        </div>
        @if(session('success') || session('error') || session('warning'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: "{{ session('success') ? '¡Éxito!' : (session('error') ? '¡Error!' : '¡Advertencia!') }}",
                        text: `{!! session('success') ?? session('error') ?? session('warning') !!}`,
                        icon: "{{ session('success') ? 'success' : (session('error') ? 'error' : 'warning') }}",
                        confirmButtonText: "OK"
                    });
                });
            </script>
        @endif
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
        <script src="{{asset('js/flowbite312.min.js')}}"></script>
        <script src="{{asset('js/app.js')}}"></script>
        @yield('scripts')
    </body>
</html>