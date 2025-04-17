<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/x-icon" href="{{asset('favicon.ico')}}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100">
        <div class="flex flex-wrap min-h-screen w-full content-center justify-center bg-slate-100 py-10">
            <div class="flex shadow-md">
                <div class="flex flex-wrap relative content-center justify-center rounded-l-md bg-white
                    w-96 h-[32rem] md:shadow-[40px_0px_30px_rgb(255,255,255)]">
                    <div class="w-72">
                        <img src="{{ asset('img/logo.png') }}" class="absolute top-6 right-5 w-20">
                        <h1 class="text-3xl font-bold cursor-pointer">Iniciar Sesión</h1>
                        <small class="text-gray-400">¡Bienvenido!, ingresa tus credenciales para poder continuar</small>
                            {{ $slot }}
                    </div>
                </div>
                <div class="md:flex flex-wrap content-center justify-center rounded-r-md hidden w-96 h-[32rem]">
                    <img src="{{ asset('img/banner.jpg') }}" class="w-full h-full bg-center bg-no-repeat bg-cover rounded-r-md object-cover">
                </div>
            </div>
        </div>
    </body>
</html>
