<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Operations') | {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        coal: '#101216',
                        paper: '#f6f4ef',
                        primary: '#d4574e',
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
        $navItems = [
            ['route' => 'operations.index', 'label' => 'Overview', 'icon' => 'fa-table-columns'],
            ['route' => 'operations.personnel.index', 'label' => 'Personnel', 'icon' => 'fa-people-group'],
            ['route' => 'operations.fast-find.index', 'label' => 'Fast Find', 'icon' => 'fa-magnifying-glass'],
        ];
    @endphp

    <div id="operationsBackdrop" class="fixed inset-0 z-40 hidden bg-coal/45 backdrop-blur-sm lg:hidden"></div>

    <div class="min-h-screen lg:grid lg:grid-cols-[18rem_1fr]">
        <aside id="operationsSidebar" class="fixed inset-y-0 left-0 z-50 flex w-[18rem] -translate-x-full flex-col border-r border-white/10 bg-coal text-white shadow-2xl shadow-coal/30 transition-transform duration-300 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 lg:shadow-none">
            <div class="border-b border-white/10 px-5 py-5">
                <div class="flex items-start justify-between gap-4">
                    <a href="{{ route('operations.index') }}" class="flex items-center gap-3">
                        <img src="{{ asset('assets/logo-white.png') }}" alt="{{ config('app.name') }}" class="h-10 w-auto shrink-0">
                        <span>
                            <span class="block text-[11px] font-semibold uppercase tracking-[.22em] text-white/42">ICT Fest</span>
                            <span class="mt-1 block text-lg font-semibold leading-none">Operations</span>
                        </span>
                    </a>

                    <button type="button" id="closeOperationsSidebar" class="grid h-10 w-10 place-items-center rounded-lg border border-white/10 text-white/62 transition hover:bg-white/10 hover:text-white lg:hidden" aria-label="Close sidebar">
                        <span class="text-xl leading-none">&times;</span>
                    </button>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-5 text-sm">
                <p class="px-3 text-[11px] font-semibold uppercase tracking-[.18em] text-white/35">Workspace</p>
                <div class="mt-2 space-y-1">
                    @foreach($navItems as $item)
                        <a href="{{ route($item['route']) }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition {{ request()->routeIs($item['route']) ? 'bg-white text-coal shadow-lg shadow-black/10' : 'text-white/68 hover:bg-white/10 hover:text-white' }}">
                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-md {{ request()->routeIs($item['route']) ? 'bg-primary text-white' : 'bg-white/10 text-white/58' }}">
                                <i class="fa-solid {{ $item['icon'] }} text-xs"></i>
                            </span>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8 border-t border-white/10 pt-5">
                    <a href="{{ route('dashboard.users.index') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 font-medium text-white/68 transition hover:bg-white/10 hover:text-white">
                        <span>Main Dashboard</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs opacity-50"></i>
                    </a>
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
            <header class="sticky top-0 z-30 border-b border-black/5 bg-paper/95 backdrop-blur-xl">
                <div class="flex min-h-[4.75rem] items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" id="openOperationsSidebar" class="relative grid h-10 w-10 shrink-0 place-items-center rounded-lg border border-black/10 bg-white text-coal shadow-sm transition hover:border-black/20 hover:bg-black/[.03] lg:hidden" aria-label="Open sidebar">
                            <span class="absolute top-3 block h-0.5 w-5 bg-current"></span>
                            <span class="absolute top-1/2 block h-0.5 w-5 -translate-y-1/2 bg-current"></span>
                            <span class="absolute bottom-3 block h-0.5 w-5 bg-current"></span>
                        </button>

                        <div class="min-w-0">
                            <h1 class="truncate text-xl font-semibold tracking-normal sm:text-2xl">@yield('page-title', 'Operations')</h1>
                            <p class="mt-1 hidden text-sm text-coal/55 sm:block">@yield('page-subtitle', 'Run on-ground workflows quickly.')</p>
                        </div>
                    </div>

                    <a href="{{ route('dashboard.users.index') }}" class="hidden rounded-lg border border-black/10 bg-white px-4 py-2 text-sm font-semibold text-coal shadow-sm transition hover:border-black/20 hover:bg-black/[.03] sm:inline-flex">
                        Main Dashboard
                    </a>
                </div>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                <div class="mx-auto max-w-7xl">
                    @if(session('status'))
                        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 shadow-sm">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-sm">
                            <p class="font-semibold">Please fix this first:</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        const operationsSidebar = document.getElementById('operationsSidebar');
        const operationsBackdrop = document.getElementById('operationsBackdrop');
        const openOperationsSidebar = document.getElementById('openOperationsSidebar');
        const closeOperationsSidebar = document.getElementById('closeOperationsSidebar');

        function openOpsSidebar() {
            operationsSidebar.classList.remove('-translate-x-full');
            operationsBackdrop.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeOpsSidebar() {
            operationsSidebar.classList.add('-translate-x-full');
            operationsBackdrop.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        openOperationsSidebar?.addEventListener('click', openOpsSidebar);
        closeOperationsSidebar?.addEventListener('click', closeOpsSidebar);
        operationsBackdrop?.addEventListener('click', closeOpsSidebar);

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeOpsSidebar();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
