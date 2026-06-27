<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <meta name="description" content="Aescala App es una plataforma para la gestión y seguimiento de proyectos de arquitectura civil.">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta name="theme-color" content="#007bff">
        <link rel="icon" type="image/png" href="{{ asset('img/favicon-96x96.png') }}" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="{{asset('img/favicon.svg')}}" />
        <link rel="shortcut icon" href="{{asset('img/favicon.ico')}}" />

        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="Aescala">
        <meta name="mobile-web-app-capable" content="yes">

        <!-- Apple Touch Icon -->
        <link rel="apple-touch-icon" href="{{ asset('img/apple-touch-icon.png') }}">
        <meta name="apple-mobile-web-app-title" content="Aescala" />

        <link rel="manifest" href="{{asset('manifest.json')}}" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="{{asset('css/custom.css')}}" rel="stylesheet" />

        <!-- Scripts -->
        <script src="{{ asset('js/FormManager.js') }}"></script>
        <script src="{{ asset('js/loader.js') }}"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>
    <body class="font-sans antialiased">
        <div class="flex flex-col min-h-screen">
            @include('layouts.sidebar')
            @include('layouts.navigation')
            <!-- Page Content -->
            <main class="sm:ml-64 ml-0 mt-6 mb-16 sm:mr-5 mx-2 p-4 rounded-2xl bg-white border-2 shadow-lg shadow-gray-300 overflow-hidden">
                <x-title-component title="{{$title??''}}" />
                @yield('content')
            </main>
            @include('layouts.footer')
        </div>
        
        <div id="loader-container"></div>
        
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
        @if (session('pdf_url'))
            <script>
                window.addEventListener('DOMContentLoaded', function () {
                    window.open(@json(session('pdf_url')), '_blank');
                });
            </script>
        @endif
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
        <script src="{{asset('js/chart.min.js')}}"></script>
        <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
        @yield('scripts')
    </body>
</html>
