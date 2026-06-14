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
    <style>
        .mobile-nav-panel {
            opacity: 0;
            pointer-events: none;
            transform: translateY(-.75rem);
        }

        .mobile-nav-panel.is-open {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }
    </style>
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

                <div class="hidden items-center gap-1 rounded-full border border-white/10 bg-white/[.04] p-1 text-sm text-white/72 md:flex">
                    <a href="{{ url('/') }}" class="rounded-full px-4 py-2 font-medium text-white shadow-sm shadow-black/20">Home</a>
                    <a href="{{ url('/#events') }}" class="rounded-full px-4 py-2 transition hover:bg-white/10 hover:text-white">Events</a>
                    <a href="{{ route('registration.status') }}" class="rounded-full px-4 py-2 transition hover:bg-white/10 hover:text-white">Status</a>
                    <a href="{{ url('/#contact') }}" class="rounded-full px-4 py-2 transition hover:bg-white/10 hover:text-white">Contact</a>
                    <a href="{{ route('about') }}" class="rounded-full px-4 py-2 transition hover:bg-white/10 hover:text-white">About</a>
                </div>

                <button
                    type="button"
                    id="mobileNavToggle"
                    class="grid h-11 w-11 place-items-center rounded-full border border-white/10 bg-white/[.04] text-white transition hover:border-volt/50 hover:text-volt md:hidden"
                    aria-label="Open navigation"
                    aria-expanded="false"
                    aria-controls="mobileNavPanel"
                >
                    <i class="fa-solid fa-bars text-lg" data-mobile-nav-icon></i>
                </button>
            </nav>

            <div
                id="mobileNavPanel"
                class="mobile-nav-panel absolute inset-x-0 top-full origin-top border-b border-white/10 bg-ink/95 px-4 pb-5 pt-3 shadow-[0_24px_70px_rgba(0,0,0,.35)] backdrop-blur-xl transition duration-200 md:hidden"
                data-mobile-nav-panel
            >
                <div class="mx-auto grid max-w-7xl gap-2 rounded-lg border border-white/10 bg-white/[.04] p-2">
                    <a href="{{ url('/') }}" class="flex items-center justify-between rounded-md px-4 py-3 text-sm font-medium text-white transition hover:bg-white/10">
                        Home
                        <i class="fa-solid fa-house text-xs text-white/36"></i>
                    </a>
                    <a href="{{ url('/#events') }}" class="flex items-center justify-between rounded-md px-4 py-3 text-sm font-medium text-white/76 transition hover:bg-white/10 hover:text-white">
                        Events
                        <i class="fa-solid fa-layer-group text-xs text-white/36"></i>
                    </a>
                    <a href="{{ route('registration.status') }}" class="flex items-center justify-between rounded-md px-4 py-3 text-sm font-medium text-white/76 transition hover:bg-white/10 hover:text-white">
                        Status
                        <i class="fa-solid fa-magnifying-glass-chart text-xs text-white/36"></i>
                    </a>
                    <a href="{{ url('/#contact') }}" class="flex items-center justify-between rounded-md px-4 py-3 text-sm font-medium text-white/76 transition hover:bg-white/10 hover:text-white">
                        Contact
                        <i class="fa-solid fa-envelope text-xs text-white/36"></i>
                    </a>
                    <a href="{{ route('about') }}" class="flex items-center justify-between rounded-md px-4 py-3 text-sm font-medium text-white/76 transition hover:bg-white/10 hover:text-white">
                        About
                        <i class="fa-solid fa-circle-info text-xs text-white/36"></i>
                    </a>
                </div>
            </div>
        </header>

        <main>
            @yield('content')
        </main>

        <footer id="contact" class="border-t border-white/10 bg-black/25">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[1.25fr_.85fr_.9fr] lg:px-8">
                <div>
                    <img src="{{ asset('assets/logo-white.png') }}" alt="{{ config('app.name') }}" class="h-12 w-auto">
                    <p class="mt-5 max-w-md text-sm leading-6 text-white/64">
                        {{ config('app.name') }} brings university teams together for programming, open API builds, data science, game creation, and esports on the IUT campus.
                    </p>
                    <a href="mailto:iutcs@iut-dhaka.edu" class="mt-6 inline-flex items-center gap-3 rounded-full border border-white/10 bg-white/[.04] px-4 py-2 text-sm font-medium text-white/72 transition hover:border-volt/60 hover:text-white">
                        <i class="fa-solid fa-envelope text-volt"></i>
                        iutcs@iut-dhaka.edu
                    </a>
                    <div class="mt-6 flex gap-3">
                        <a href="https://www.facebook.com/IUTCS/" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="grid h-11 w-11 place-items-center rounded-full border border-white/10 bg-white/[.04] text-white/72 transition hover:-translate-y-0.5 hover:border-[#1877F2]/70 hover:text-[#1877F2]"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://bd.linkedin.com/company/iutcs" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" class="grid h-11 w-11 place-items-center rounded-full border border-white/10 bg-white/[.04] text-white/72 transition hover:-translate-y-0.5 hover:border-[#0A66C2]/70 hover:text-[#0A66C2]"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="https://www.youtube.com/channel/UCPVwRaP-wK6lSUEqTK7iLng" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="grid h-11 w-11 place-items-center rounded-full border border-white/10 bg-white/[.04] text-white/72 transition hover:-translate-y-0.5 hover:border-[#FF0033]/70 hover:text-[#FF0033]"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

                <div class="rounded-lg border border-white/10 bg-white/[.035] p-6">
                    <h2 class="text-sm font-semibold uppercase tracking-[.22em] text-white/54">Festival Desk</h2>
                    <ul class="mt-5 space-y-3 text-sm text-white/68">
                        <li class="flex gap-3"><i class="fa-solid fa-location-dot mt-1 text-ember"></i><span>Islamic University of Technology, Gazipur</span></li>
                        <li class="flex gap-3"><i class="fa-solid fa-trophy mt-1 text-ember"></i><span>Onsite round at 24-25 July 2026.</span></li>
                        <li class="flex gap-3"><i class="fa-solid fa-id-card mt-1 text-ember"></i><span>Valid student ID is required for eligible participants.</span></li>
                    </ul>
                </div>

                <div class="rounded-lg border border-white/10 bg-white/[.035] p-6">
                    <h2 class="text-sm font-semibold uppercase tracking-[.22em] text-white/54">Quick Links</h2>
                    <div class="mt-5 grid grid-cols-2 gap-3 text-sm text-white/68">
                        <a href="{{ url('/') }}" class="transition hover:text-white">Home</a>
                        <a href="{{ url('/#events') }}" class="transition hover:text-white">Events</a>
                        <a href="{{ url('/#schedule') }}" class="transition hover:text-white">Schedule</a>
                        <a href="{{ route('registration.status') }}" class="transition hover:text-white">Status</a>
                        <a href="{{ route('about') }}" class="transition hover:text-white">About</a>
                        <a href="mailto:iutcs@iut-dhaka.edu" class="transition hover:text-white">Contact</a>
                        <a href="{{ url('/#events') }}" class="transition hover:text-white">Registration</a>
                    </div>
                </div>
            </div>

            <div class="border-t border-white/10 px-4 py-5 text-xs text-white/42">
                <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 text-center sm:flex-row sm:text-left">
                    <span>&copy; {{ date('Y') }} IUT Computer Society. All rights reserved.</span>
                    <span>
                        Built with ❤️ by
                        <a href="https://portfolio.sakhawatadib.com" target="_blank" rel="noopener noreferrer" class="font-medium text-white/70 transition hover:text-volt">Adib Sakhawat</a>
                    </span>
                </div>
            </div>
        </footer>
    </div>
    <script>
        (() => {
            const toggle = document.getElementById('mobileNavToggle');
            const panel = document.querySelector('[data-mobile-nav-panel]');
            const icon = document.querySelector('[data-mobile-nav-icon]');

            if (!toggle || !panel || !icon) {
                return;
            }

            const setOpen = (isOpen) => {
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                panel.classList.toggle('is-open', isOpen);
                icon.classList.toggle('fa-bars', !isOpen);
                icon.classList.toggle('fa-xmark', isOpen);
            };

            toggle.addEventListener('click', () => {
                setOpen(toggle.getAttribute('aria-expanded') !== 'true');
            });

            panel.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => setOpen(false));
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    setOpen(false);
                }
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
