<x-layout title="Manajemen Tugas - Admin" active-nav="assignments">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <header class="mb-space-lg">
            <div class="flex items-center gap-space-xs mb-space-2xs">
                <span class="px-space-xs py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm uppercase">Akademik</span>
                <span class="w-1 h-1 rounded-full bg-outline"></span>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Aktivitas perkuliahan</span>
            </div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface">Manajemen Tugas</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">Pantau tugas dan submission dari seluruh mata kuliah.</p>
        </header>

        @if (session('success'))
            <div class="mb-space-md rounded-xl border border-primary/20 bg-primary/10 px-space-md py-space-sm text-primary">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="mb-space-md rounded-xl border border-error/20 bg-error/10 px-space-md py-space-sm text-error">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-space-md rounded-xl border border-error/20 bg-error/10 px-space-md py-space-sm text-error">Periksa kembali data yang dimasukkan.</div>
        @endif

        <section class="bg-surface-container-low p-space-sm rounded-2xl border border-outline/10 shadow-sm mb-space-md">
            <form method="GET" action="{{ route('admin.assignments.index') }}" class="flex flex-col md:flex-row gap-space-sm">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari judul, instruksi, atau mata kuliah..."
                    class="flex-1 min-w-0 bg-surface-container text-on-surface placeholder:text-outline border border-outline/10 rounded-xl px-space-md py-2.5 focus:outline-none focus:ring-2 focus:ring-primary">
                <select name="course_id" class="bg-surface-container text-on-surface border border-outline/10 rounded-xl px-space-md py-2.5 focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">Semua Mata Kuliah</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @selected((string) request('course_id') === (string) $course->id)>{{ $course->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-space-md py-2.5 rounded-xl bg-primary text-on-primary font-label-md hover:bg-primary-container transition-colors">Terapkan</button>
                @if (request()->filled('search') || request()->filled('course_id'))
                    <a href="{{ route('admin.assignments.index') }}" class="px-space-sm py-2.5 rounded-xl bg-surface-container-high text-on-surface-variant hover:text-on-surface text-center">Reset</a>
                @endif
            </form>
        </section>

        <section class="bg-surface-container-low rounded-2xl border border-outline/10 shadow-md overflow-hidden" aria-label="Daftar tugas">
            <div class="px-space-md py-space-sm border-b border-outline/10 flex items-center justify-between gap-space-sm">
                <div>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Daftar Tugas</h2>
                    <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $assignments->total() }} dari {{ $totalAssignments }} tugas</span>
                </div>
                <a href="{{ route('admin.assignments.create') }}" class="inline-flex items-center gap-space-xs rounded-xl bg-primary px-space-md py-2.5 font-label-md text-on-primary hover:bg-primary-container transition-colors">
                    <span class="material-symbols-outlined text-base">add</span>Tambah Tugas
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left">
                    <thead class="bg-surface-container text-on-surface-variant">
                    <tr>
                        <th class="px-space-md py-space-sm font-label-sm text-label-sm uppercase">Mata Kuliah</th>
                        <th class="px-space-md py-space-sm font-label-sm text-label-sm uppercase">Judul Tugas</th>
                        <th class="px-space-md py-space-sm font-label-sm text-label-sm uppercase">Deadline</th>
                        <th class="px-space-md py-space-sm font-label-sm text-label-sm uppercase">Status</th>
                        <th class="px-space-md py-space-sm font-label-sm text-label-sm uppercase">Submission</th>
                        <th class="px-space-md py-space-sm font-label-sm text-label-sm uppercase text-right">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-outline/10">
                        @forelse ($assignments as $assignment)
                            <tr class="hover:bg-surface-container transition-colors">
                                <td class="px-space-md py-space-sm">
                                    <span class="font-label-md text-label-md text-on-surface">{{ $assignment->course?->name ?? 'Mata kuliah tidak ditemukan' }}</span>
                                    <span class="block font-label-sm text-label-sm text-outline">{{ $assignment->course?->code ?? '—' }}</span>
                                </td>
                                <td class="px-space-md py-space-sm">
                                    <span class="font-label-md text-label-md text-on-surface">{{ $assignment->title }}</span>
                                    <span class="block font-body-sm text-body-sm text-on-surface-variant">Nilai maksimal {{ $assignment->max_score }}</span>
                                </td>
                                <td class="px-space-md py-space-sm font-body-sm text-body-sm text-on-surface-variant">{{ $assignment->due_at?->format('d M Y, H:i') ?? '—' }}</td>
                                <td class="px-space-md py-space-sm">
                                    <span class="inline-flex px-2.5 py-1 rounded-full font-label-sm text-label-sm {{ $assignment->status === 'published' ? 'bg-primary/10 text-primary' : 'bg-surface-container-high text-on-surface-variant' }}">{{ ucfirst($assignment->status) }}</span>
                                </td>
                                <td class="px-space-md py-space-sm font-label-md text-label-md text-on-surface">{{ number_format($assignment->submissions_count) }}</td>
                                <td class="px-space-md py-space-sm">
                                    <div class="flex items-center justify-end gap-space-xs">
                                        <a href="{{ route('admin.assignments.edit', $assignment) }}" class="inline-flex items-center gap-1 rounded-lg bg-surface-container-high px-3 py-2 font-label-sm text-on-surface hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined text-sm">edit</span>Edit
                                        </a>
                                        @if ($assignment->submissions_count === 0)
                                            <form action="{{ route('admin.assignments.destroy', $assignment) }}" method="POST" onsubmit="return confirm('Hapus tugas ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-error/10 px-3 py-2 font-label-sm text-error hover:bg-error/20 transition-colors">
                                                    <span class="material-symbols-outlined text-sm">delete</span>Hapus
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" disabled title="Tugas dengan submission tidak dapat dihapus" class="inline-flex items-center gap-1 rounded-lg bg-surface-container-high px-3 py-2 font-label-sm text-outline cursor-not-allowed">
                                                <span class="material-symbols-outlined text-sm">delete</span>Hapus
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-space-md py-space-xl text-center text-on-surface-variant">{{ request()->filled('search') || request()->filled('course_id') ? 'Tidak ada tugas yang cocok dengan filter.' : 'Belum ada tugas di database. Gunakan Tambah Tugas untuk membuat data pertama.' }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($assignments->hasPages())
                <div class="px-space-md py-space-sm border-t border-outline/10 flex justify-center">
                    {{ $assignments->links('vendor.pagination.custom-pagination') }}
                </div>
            @endif
        </section>
    </div>
</x-layout>