<div class="mt-6 rounded-lg border border-white/10 bg-black/20 p-5">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-xs font-medium uppercase tracking-[.22em] text-white/38">Payment Instruction</p>
            <h3 class="mt-3 text-2xl font-semibold text-white">{{ $amount }}</h3>
            <p class="mt-3 text-sm leading-6 text-white/58">
                Send Money to this receiver number. It supports both bKash and Nagad.
            </p>
            <div class="mt-4 inline-flex items-center gap-3 rounded-md border border-volt/30 bg-volt/10 px-4 py-3">
                <span class="grid h-9 w-9 place-items-center rounded-md bg-volt/15 text-volt">
                    <i class="fa-solid fa-mobile-screen-button"></i>
                </span>
                <span>
                    <span class="block text-[10px] font-semibold uppercase tracking-[.2em] text-volt/80">Receiver Number</span>
                    <span class="mt-1 block font-mono text-2xl font-semibold tracking-normal text-white sm:text-3xl">{{ $number }}</span>
                </span>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:min-w-[20rem]">
            <div class="rounded-md border border-white/10 bg-white/[.04] p-4">
                <img src="{{ asset('assets/bkash.webp') }}" alt="bKash" class="h-8 w-auto">
                <p class="mt-3 text-xs font-semibold uppercase tracking-[.18em] text-white/38">Method</p>
                <p class="mt-1 text-sm font-semibold text-white">Send Money</p>
            </div>
            <div class="rounded-md border border-white/10 bg-white/[.04] p-4">
                <img src="{{ asset('assets/nagad.png') }}" alt="Nagad" class="h-8 w-auto">
                <p class="mt-3 text-xs font-semibold uppercase tracking-[.18em] text-white/38">Method</p>
                <p class="mt-1 text-sm font-semibold text-white">Send Money</p>
            </div>
        </div>
    </div>
</div>
