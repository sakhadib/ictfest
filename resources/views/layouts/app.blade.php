<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#050816">

    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/logo-white.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        ink: '#050816',
                        panel: '#0b1022',
                        line: 'rgba(255,255,255,.12)',
                        volt: '#7cf7d4',
                        ember: '#ffb454',
                        iris: '#9b8cff',
                    },
                    boxShadow: {
                        glow: '0 24px 90px rgba(124, 247, 212, .16)',
                    },
                },
            },
        };
    </script>
    @stack('styles')
</head>
<body class="bg-ink text-white antialiased">
    <div class="min-h-screen overflow-hidden bg-[radial-gradient(circle_at_50%_-10%,rgba(124,247,212,.09),transparent_34%),linear-gradient(180deg,#050816_0%,#080b14_48%,#050816_100%)]">
        <header class="fixed inset-x-0 top-0 z-50 border-b border-white/10 bg-ink/80 backdrop-blur-xl">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <img src="{{ asset('assets/logo-white.png') }}" alt="{{ config('app.name') }}" class="h-10 w-auto">
                    <span class="hidden text-sm font-semibold uppercase tracking-[.24em] text-white/80 sm:block">{{ config('app.name') }}</span>
                </a>

                <div class="flex items-center gap-1 rounded-full border border-white/10 bg-white/[.04] p-1 text-sm text-white/72">
                    <a href="{{ url('/') }}" class="rounded-full px-4 py-2 font-medium text-white shadow-sm shadow-black/20">Home</a>
                    <a href="{{ url('/#events') }}" class="rounded-full px-4 py-2 transition hover:bg-white/10 hover:text-white">Events</a>
                    <a href="{{ route('registration.status') }}" class="rounded-full px-4 py-2 transition hover:bg-white/10 hover:text-white">Status</a>
                    <a href="{{ url('/#contact') }}" class="hidden rounded-full px-4 py-2 transition hover:bg-white/10 hover:text-white sm:inline-flex">Contact</a>
                    <a href="{{ url('/#about') }}" class="hidden rounded-full px-4 py-2 transition hover:bg-white/10 hover:text-white sm:inline-flex">About</a>
                </div>
            </nav>
        </header>

        <main>
            @yield('content')
        </main>

        <footer id="contact" class="border-t border-white/10 bg-black/20">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 lg:grid-cols-[1.15fr_.85fr_.85fr] lg:px-8">
                <div>
                    <img src="{{ asset('assets/logo-white.png') }}" alt="{{ config('app.name') }}" class="h-12 w-auto">
                    <p class="mt-5 max-w-md text-sm leading-6 text-white/64">
                        {{ config('app.name') }} brings university teams together for programming, open API builds, data science, game creation, and esports on the IUT campus.
                    </p>
                    <div class="mt-6 flex gap-3">
                        <a href="#" aria-label="Facebook" class="grid h-10 w-10 place-items-center rounded-full border border-white/10 bg-white/[.04] text-white/72 transition hover:border-volt/60 hover:text-volt"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" aria-label="LinkedIn" class="grid h-10 w-10 place-items-center rounded-full border border-white/10 bg-white/[.04] text-white/72 transition hover:border-volt/60 hover:text-volt"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#" aria-label="Instagram" class="grid h-10 w-10 place-items-center rounded-full border border-white/10 bg-white/[.04] text-white/72 transition hover:border-volt/60 hover:text-volt"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" aria-label="Discord" class="grid h-10 w-10 place-items-center rounded-full border border-white/10 bg-white/[.04] text-white/72 transition hover:border-volt/60 hover:text-volt"><i class="fa-brands fa-discord"></i></a>
                    </div>
                </div>

                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-[.22em] text-white/54">Festival Desk</h2>
                    <ul class="mt-5 space-y-3 text-sm text-white/68">
                        <li class="flex gap-3"><i class="fa-solid fa-location-dot mt-1 text-ember"></i><span>Islamic University of Technology, Gazipur</span></li>
                        <li class="flex gap-3"><i class="fa-solid fa-calendar-days mt-1 text-ember"></i><span>Registration opens 18 June and major onsite rounds run 24-25 July 2026.</span></li>
                        <li class="flex gap-3"><i class="fa-solid fa-id-card mt-1 text-ember"></i><span>Valid student ID is required for eligible participants.</span></li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-[.22em] text-white/54">Quick Links</h2>
                    <div class="mt-5 grid grid-cols-2 gap-3 text-sm text-white/68">
                        <a href="{{ url('/#events') }}" class="transition hover:text-white">Events</a>
                        <a href="{{ url('/#schedule') }}" class="transition hover:text-white">Schedule</a>
                        <a href="{{ route('registration.status') }}" class="transition hover:text-white">Status</a>
                        <a href="{{ url('/#about') }}" class="transition hover:text-white">About</a>
                        <a href="#" class="transition hover:text-white">Rulebooks</a>
                        <a href="#" class="transition hover:text-white">Registration</a>
                    </div>
                </div>
            </div>

            <div class="border-t border-white/10 px-4 py-5 text-center text-xs text-white/42">
                <span>&copy; {{ date('Y') }} {{ config('app.name') }}. Organized for the CSE community.</span>
            </div>
        </footer>
    </div>
    @stack('scripts')
</body>
</html>
