@php
    $items = [
        ['label' => 'Compose', 'route' => 'dashboard.emails.compose', 'icon' => '01'],
        ['label' => 'Recipients', 'route' => 'dashboard.emails.recipients', 'icon' => '02'],
        ['label' => 'Review', 'route' => 'dashboard.emails.review', 'icon' => '03'],
        ['label' => 'Logs', 'route' => 'dashboard.email-logs.index', 'icon' => 'Log'],
    ];
@endphp

<div class="mb-6 rounded-2xl border border-black/5 bg-white p-2 shadow-soft">
    <nav class="grid gap-2 text-sm md:grid-cols-4">
        @foreach($items as $item)
            @php
                $active = request()->routeIs($item['route'])
                    || ($item['route'] === 'dashboard.email-logs.index' && (request()->routeIs('dashboard.email-logs.*') || request()->routeIs('dashboard.emails.history') || request()->routeIs('dashboard.emails.show')));
            @endphp
            <a href="{{ route($item['route']) }}" class="flex items-center gap-3 rounded-xl px-3 py-3 font-semibold transition {{ $active ? 'bg-primary text-white shadow-lg shadow-primary/15' : 'text-coal/62 hover:bg-paper hover:text-coal' }}">
                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg {{ $active ? 'bg-white/20 text-white' : 'bg-paper text-coal/45' }} text-xs">
                    {{ $item['icon'] }}
                </span>
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>
</div>
