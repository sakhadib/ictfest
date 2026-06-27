<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') | {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        coal: '#101216',
                        paper: '#f6f4ef',
                        primary: '#d4574e',
                        saffron: '#f4b23f',
                    },
                    boxShadow: {
                        soft: '0 18px 60px rgba(16, 18, 22, .10)',
                    },
                },
            },
        };
    </script>
</head>
<body class="bg-paper text-coal antialiased">
    @php
        $dashboardEvents = \App\Models\Event::orderBy('code')->get();
    @endphp

    <div id="sidebarBackdrop" class="fixed inset-0 z-40 hidden bg-coal/45 backdrop-blur-sm lg:hidden"></div>

    <div class="min-h-screen lg:grid lg:grid-cols-[19rem_1fr]">
        <aside id="dashboardSidebar" class="fixed inset-y-0 left-0 z-50 flex w-[19rem] -translate-x-full flex-col border-r border-white/10 bg-coal text-white shadow-2xl shadow-coal/30 transition-transform duration-300 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 lg:shadow-none">
            <div class="border-b border-white/10 px-5 py-5">
                <div class="flex items-start justify-between gap-4">
                    <a href="{{ route('dashboard.users.index') }}" class="group flex items-center gap-3">
                        <img src="{{ asset('assets/logo-white.png') }}" alt="{{ config('app.name') }}" class="h-11 w-auto shrink-0">
                        <span>
                            <span class="block text-[11px] font-semibold uppercase tracking-[.22em] text-white/45">12th IUT</span>
                            <span class="mt-1 block text-lg font-semibold leading-none">ICT Fest</span>
                        </span>
                    </a>

                    <button type="button" id="closeSidebar" class="grid h-10 w-10 place-items-center rounded-lg border border-white/10 text-white/62 transition hover:bg-white/10 hover:text-white lg:hidden" aria-label="Close sidebar">
                        <span class="text-xl leading-none">&times;</span>
                    </button>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-5 text-sm">
                <div class="mb-6">
                    <p class="px-3 text-[11px] font-semibold uppercase tracking-[.18em] text-white/35">Main</p>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('dashboard.users.index') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('dashboard.users.*') ? 'bg-white text-coal shadow-lg shadow-black/10' : 'text-white/68 hover:bg-white/10 hover:text-white' }}">
                            <span>Users</span>
                            <span class="text-xs opacity-50">Manage</span>
                        </a>
                        <a href="{{ route('dashboard.status.index') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('dashboard.status.*') ? 'bg-white text-coal shadow-lg shadow-black/10' : 'text-white/68 hover:bg-white/10 hover:text-white' }}">
                            <span>Status</span>
                            <span class="text-xs opacity-50">Lookup</span>
                        </a>
                        <a href="{{ route('dashboard.reports.index') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('dashboard.reports.*') ? 'bg-white text-coal shadow-lg shadow-black/10' : 'text-white/68 hover:bg-white/10 hover:text-white' }}">
                            <span>Report</span>
                            <span class="text-xs opacity-50">CSV</span>
                        </a>
                        <a href="{{ route('dashboard.emails.index') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('dashboard.emails.*') ? 'bg-white text-coal shadow-lg shadow-black/10' : 'text-white/68 hover:bg-white/10 hover:text-white' }}">
                            <span>Email</span>
                            <span class="text-xs opacity-50">Queue</span>
                        </a>
                        <a href="{{ route('dashboard.email-logs.index') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('dashboard.email-logs.*') ? 'bg-white text-coal shadow-lg shadow-black/10' : 'text-white/68 hover:bg-white/10 hover:text-white' }}">
                            <span>Email Logs</span>
                            <span class="text-xs opacity-50">Monitor</span>
                        </a>
                        <a href="{{ route('dashboard.event-status.index') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('dashboard.event-status.*') ? 'bg-white text-coal shadow-lg shadow-black/10' : 'text-white/68 hover:bg-white/10 hover:text-white' }}">
                            <span>Event Status</span>
                            <span class="text-xs opacity-50">Live</span>
                        </a>
                    </div>
                </div>

                <div>
                    <p class="px-3 text-[11px] font-semibold uppercase tracking-[.18em] text-white/35">Events</p>
                    <div class="mt-2 space-y-1">
                        @foreach($dashboardEvents as $navEvent)
                            <a href="{{ route('dashboard.events.registrations.index', ['event' => $navEvent->code]) }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs('dashboard.events.*') && request()->route('event')?->is($navEvent) ? 'bg-white text-coal shadow-lg shadow-black/10' : 'text-white/68 hover:bg-white/10 hover:text-white' }}">
                                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-md {{ request()->routeIs('dashboard.events.*') && request()->route('event')?->is($navEvent) ? 'bg-primary text-white' : 'bg-white/10 text-white/58' }} text-xs font-semibold">
                                    {{ $navEvent->code }}
                                </span>
                                <span class="min-w-0 truncate">{{ $navEvent->code === '01' ? 'IUPC' : $navEvent->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </nav>

            <div class="border-t border-white/10 p-4">
                <div class="rounded-lg bg-white/[.06] p-3">
                    <p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p>
                    <p class="mt-1 truncate text-xs text-white/45">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </aside>

        <div class="min-w-0">
            <header class="sticky top-0 z-30 border-b border-black/5 bg-paper/90 backdrop-blur-xl">
                <div class="flex min-h-[4.75rem] items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" id="openSidebar" class="relative grid h-10 w-10 shrink-0 place-items-center rounded-lg border border-black/10 bg-white text-coal shadow-sm transition hover:border-black/20 hover:bg-black/[.03] lg:hidden" aria-label="Open sidebar">
                            <span class="absolute top-3 block h-0.5 w-5 bg-current"></span>
                            <span class="absolute top-1/2 block h-0.5 w-5 -translate-y-1/2 bg-current"></span>
                            <span class="absolute bottom-3 block h-0.5 w-5 bg-current"></span>
                        </button>

                        <div class="min-w-0">
                            <h1 class="truncate text-xl font-semibold tracking-normal sm:text-2xl">@yield('page-title', 'Dashboard')</h1>
                            <p class="mt-1 hidden text-sm text-coal/55 sm:block">@yield('page-subtitle', 'Manage system records.')</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-lg border border-black/10 bg-white px-3 py-2 text-sm font-semibold text-coal shadow-sm transition hover:border-black/20 hover:bg-black/[.03] sm:px-4">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                <div class="mx-auto max-w-7xl">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('dashboardSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const openButton = document.getElementById('openSidebar');
        const closeButton = document.getElementById('closeSidebar');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        openButton?.addEventListener('click', openSidebar);
        closeButton?.addEventListener('click', closeSidebar);
        backdrop?.addEventListener('click', closeSidebar);

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });
    </script>
</body>
</html>
