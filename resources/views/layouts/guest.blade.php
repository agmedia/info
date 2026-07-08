<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>ALPHA CAPITALIS</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .guest-auth-card {
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .auth-remember-checkbox {
                color: #0f2a43;
            }

            .auth-remember-checkbox:focus {
                border-color: #ab8d52;
                --tw-ring-color: #ab8d52;
            }

            .auth-primary-button {
                background-color: #0f2a43 !important;
            }

            .auth-primary-button:hover,
            .auth-primary-button:focus {
                background-color: #143a5c !important;
            }

            .auth-primary-button:active {
                background-color: #071326 !important;
            }

            .auth-primary-button:focus {
                --tw-ring-color: #ab8d52;
            }
        </style>
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div
            class="min-h-screen flex flex-col sm:justify-center items-center px-4 py-8 sm:px-6"
            style="background: radial-gradient(120% 160% at 82% -44%, rgba(4, 86, 146, 0.28), transparent 58%), linear-gradient(90deg, #050607 0%, #07090c 30%, #07213a 58%, #0a3d64 100%);"
        >
            <div class="flex justify-center">
                <a href="/" wire:navigate>
                    <img
                        src="{{ asset('front-theme/images/branding/alpha-capitalis-logo.svg') }}"
                        alt="Alpha Capitalis"
                        class="h-16 w-auto sm:h-20"
                    >
                </a>
            </div>

            <div class="guest-auth-card w-full sm:max-w-md mt-8 px-6 py-5 bg-white overflow-hidden rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
