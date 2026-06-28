<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --surface-1: #f8fafc;
            --text-primary: #0f172a;
            --text-tertiary: #64748b;
        }
        .dark {
            --surface-1: #0f172a;
            --text-primary: #f8fafc;
            --text-tertiary: #94a3b8;
        }
    </style>
</head>
<body class="h-full antialiased dark:bg-slate-900" style="background:var(--surface-1);">
    <div class="min-h-full flex flex-col justify-center items-center px-6 py-12">
        <div class="text-center">
            <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-500">@yield('code')</p>
            <h1 class="mt-4 text-4xl font-extrabold tracking-tight" style="color:var(--text-primary);">@yield('message')</h1>
            <p class="mt-6 text-base leading-7" style="color:var(--text-tertiary);">@yield('description')</p>
            <div class="mt-10 flex items-center justify-center gap-x-6">
                <a href="{{ url('/') }}" class="btn btn-primary px-6 py-2.5">
                    Go back home
                </a>
            </div>
        </div>
    </div>
    <script>
        if (localStorage.getItem('flora_dark') === 'true' ||
            (!('flora_dark' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</body>
</html>
