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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
    </head>
    <body class="font-sans antialiased overflow-y-scroll">
        <div class="flex flex-col h-full w-full">
            @include('layouts.sidebar')
            @include('layouts.navigation')
            <!-- Page Content -->
            <main class="sm:ml-64 ml-0 h-full max-w-full sm:mt-6 mt-2 sm:mb-16 mb-1 sm:mr-5 p-4 rounded-2xl bg-white border-2 shadow-lg shadow-gray-300">
                <x-title-component title="{{$title??''}}" />
                @yield('content')
            </main>
            @include('layouts.footer')
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
            <script src="{{asset('js/flowbite312.min.js')}}"></script>
            <script src="{{asset('js/app.js')}}"></script>

            @yield('scripts')
    </body>
</html>