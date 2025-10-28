@props(['title' => 'LeadSpark CRM'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    @unless(app()->environment('testing'))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endunless
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
    <div class="flex min-h-screen flex-col">
        <header class="bg-white shadow-sm">
            <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-4 py-4">
                <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-slate-900">
                    LeadSpark CRM
                </a>
                @if(session()->get('crm_authenticated', false))
                    <form method="POST" action="{{ route('crm.logout') }}">
                        @csrf
                        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900">
                            Logout
                        </button>
                    </form>
                @endif
            </div>
        </header>

        <main class="flex-1">
            <div class="mx-auto w-full max-w-6xl px-4 py-10">
                @isset($title)
                    <h1 class="mb-6 text-2xl font-semibold text-slate-900">{{ $title }}</h1>
                @endisset

                @if ($errors->any())
                    <div class="mb-6 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        <p class="font-semibold">There were some problems with your submission:</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>

        <footer class="bg-white">
            <div class="mx-auto w-full max-w-6xl px-4 py-4 text-xs text-slate-500">
                &copy; {{ now()->year }} LeadSpark CRM
            </div>
        </footer>
    </div>
</body>
</html>
