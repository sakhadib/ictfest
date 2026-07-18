@extends('layouts.operations')

@section('title', 'Personnel')
@section('page-title', 'Personnel')
@section('page-subtitle', 'Import, add, edit, and remove operations personnel.')

@section('content')
    @php
        $inputClass = 'mt-2 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-base outline-none transition focus:border-primary focus:ring-4 focus:ring-primary/10';
        $labelClass = 'text-xs font-semibold uppercase tracking-[.16em] text-coal/45';
    @endphp

    <section class="rounded-2xl border border-black/5 bg-white shadow-soft">
        <div class="border-b border-black/5 p-5 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[.18em] text-primary">Personnel</p>
                    <h2 class="mt-2 text-2xl font-semibold">Current operational team</h2>
                    <p class="mt-2 text-sm leading-6 text-coal/55">{{ $personnel->total() }} record{{ $personnel->total() === 1 ? '' : 's' }} in the personnel directory.</p>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:flex">
                    <button type="button" data-open-modal="uploadPersonnelModal" class="rounded-xl border border-black/10 bg-white px-4 py-3 text-sm font-semibold text-coal shadow-sm transition hover:bg-black/[.03]">
                        Upload CSV
                    </button>
                    <button type="button" data-open-modal="addPersonnelModal" class="rounded-xl bg-primary px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-primary/15 transition hover:bg-[#bd453d]">
                        Add Person
                    </button>
                </div>
            </div>
        </div>

        <div class="grid gap-4 p-4 sm:p-5 lg:hidden">
            @forelse($personnel as $person)
                <article class="rounded-2xl border border-black/5 bg-paper p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <h3 class="break-words text-lg font-semibold">{{ $person->name }}</h3>
                            <p class="mt-1 break-words text-sm text-coal/55">{{ ucfirst($person->status) }} / {{ $person->team ?: 'No team' }}</p>
                            <p class="mt-2 text-sm font-semibold text-primary">{{ $person->phone ?: 'No phone' }}</p>
                        </div>
                        <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-coal/55">{{ $person->student_id ?: 'No ID' }}</span>
                    </div>

                    @if($person->comments)
                        <p class="mt-4 rounded-xl bg-white px-3 py-2 text-sm leading-6 text-coal/58">{{ $person->comments }}</p>
                    @endif

                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <button type="button" data-open-modal="editPersonnelModal{{ $person->id }}" class="rounded-xl bg-white px-4 py-3 text-sm font-semibold text-coal shadow-sm">
                            Edit
                        </button>
                        <form method="POST" action="{{ route('operations.personnel.destroy', ['personnel' => $person]) }}" onsubmit="return confirm('Delete this personnel record?')">
                            @csrf
                            @method('DELETE')
                            <button class="w-full rounded-xl border border-red-200 bg-white px-4 py-3 text-sm font-semibold text-red-700">
                                Delete
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-black/10 bg-paper px-4 py-12 text-center text-sm text-coal/50">
                    No personnel records yet.
                </div>
            @endforelse
        </div>

        <div class="hidden overflow-x-auto lg:block">
            <table class="min-w-full divide-y divide-black/5 text-left text-sm">
                <thead class="bg-paper text-xs uppercase tracking-wide text-coal/45">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Name</th>
                        <th class="px-5 py-3 font-semibold">Phone</th>
                        <th class="px-5 py-3 font-semibold">Team</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold">Comments</th>
                        <th class="px-5 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @forelse($personnel as $person)
                        <tr>
                            <td class="px-5 py-4">
                                <p class="font-semibold">{{ $person->name }}</p>
                                <p class="mt-1 text-xs text-coal/45">{{ $person->student_id ?: 'No student ID' }}</p>
                            </td>
                            <td class="px-5 py-4 font-semibold text-primary">{{ $person->phone ?: '-' }}</td>
                            <td class="px-5 py-4 text-coal/65">{{ $person->team ?: '-' }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-paper px-3 py-1 text-xs font-semibold capitalize text-coal/60">{{ $person->status }}</span>
                            </td>
                            <td class="max-w-xs px-5 py-4 text-coal/55">{{ str($person->comments ?: '-')->limit(90) }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" data-open-modal="editPersonnelModal{{ $person->id }}" class="rounded-lg border border-black/10 bg-white px-3 py-2 text-sm font-semibold text-coal shadow-sm transition hover:bg-black/[.03]">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('operations.personnel.destroy', ['personnel' => $person]) }}" onsubmit="return confirm('Delete this personnel record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg border border-red-200 bg-white px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-coal/50">No personnel records yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($personnel->hasPages())
            <div class="border-t border-black/5 p-5">
                {{ $personnel->links() }}
            </div>
        @endif
    </section>

    <div id="addPersonnelModal" class="fixed inset-0 z-[70] hidden overflow-y-auto bg-coal/55 px-4 py-6 backdrop-blur-sm" data-modal>
        <div class="mx-auto max-w-xl rounded-2xl bg-paper p-5 shadow-2xl sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[.18em] text-primary">Manual Add</p>
                    <h2 class="mt-2 text-2xl font-semibold">Add personnel</h2>
                </div>
                <button type="button" data-close-modal class="grid h-10 w-10 place-items-center rounded-xl bg-white text-xl leading-none shadow-sm">&times;</button>
            </div>
            <form method="POST" action="{{ route('operations.personnel.store') }}" class="mt-5 grid gap-4">
                @csrf
                @include('operations.partials.personnel-fields', ['person' => null, 'statuses' => $statuses, 'inputClass' => $inputClass, 'labelClass' => $labelClass])
                <button class="rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-primary/15 transition hover:bg-[#bd453d]">
                    Add Personnel
                </button>
            </form>
        </div>
    </div>

    <div id="uploadPersonnelModal" class="fixed inset-0 z-[70] hidden overflow-y-auto bg-coal/55 px-4 py-6 backdrop-blur-sm" data-modal>
        <div class="mx-auto max-w-xl rounded-2xl bg-paper p-5 shadow-2xl sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[.18em] text-primary">CSV Upload</p>
                    <h2 class="mt-2 text-2xl font-semibold">Import personnel</h2>
                </div>
                <button type="button" data-close-modal class="grid h-10 w-10 place-items-center rounded-xl bg-white text-xl leading-none shadow-sm">&times;</button>
            </div>
            <p class="mt-4 text-sm leading-6 text-coal/60">
                Required columns: <strong>name</strong>, <strong>student_id</strong>, <strong>phone</strong>, <strong>team</strong>, <strong>status</strong>. Optional: <strong>comments</strong>. Status must be volunteer, organizer, staff, faculty, or other.
            </p>
            <form method="POST" action="{{ route('operations.personnel.upload') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                @csrf
                <input name="personnel_csv" type="file" accept=".csv,text/csv" class="block w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white">
                <button class="w-full rounded-xl bg-coal px-5 py-3 text-sm font-semibold text-white transition hover:bg-black">
                    Upload CSV
                </button>
            </form>
        </div>
    </div>

    @foreach($personnel as $person)
        <div id="editPersonnelModal{{ $person->id }}" class="fixed inset-0 z-[70] hidden overflow-y-auto bg-coal/55 px-4 py-6 backdrop-blur-sm" data-modal>
            <div class="mx-auto max-w-xl rounded-2xl bg-paper p-5 shadow-2xl sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[.18em] text-primary">Edit Personnel</p>
                        <h2 class="mt-2 break-words text-2xl font-semibold">{{ $person->name }}</h2>
                    </div>
                    <button type="button" data-close-modal class="grid h-10 w-10 place-items-center rounded-xl bg-white text-xl leading-none shadow-sm">&times;</button>
                </div>
                <form method="POST" action="{{ route('operations.personnel.update', ['personnel' => $person]) }}" class="mt-5 grid gap-4">
                    @csrf
                    @method('PATCH')
                    @include('operations.partials.personnel-fields', ['person' => $person, 'statuses' => $statuses, 'inputClass' => $inputClass, 'labelClass' => $labelClass])
                    <button class="rounded-xl bg-coal px-5 py-3 text-sm font-semibold text-white transition hover:bg-black">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script>
        const openModalButtons = document.querySelectorAll('[data-open-modal]');
        const closeModalButtons = document.querySelectorAll('[data-close-modal]');
        const modals = document.querySelectorAll('[data-modal]');

        function openModal(id) {
            document.getElementById(id)?.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeModals() {
            modals.forEach((modal) => modal.classList.add('hidden'));
            document.body.classList.remove('overflow-hidden');
        }

        openModalButtons.forEach((button) => {
            button.addEventListener('click', () => openModal(button.dataset.openModal));
        });

        closeModalButtons.forEach((button) => button.addEventListener('click', closeModals));
        modals.forEach((modal) => {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModals();
                }
            });
        });
    </script>
@endpush
