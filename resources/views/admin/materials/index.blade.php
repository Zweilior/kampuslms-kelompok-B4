<x-layout title="Manajemen Materi - Admin" active-nav="materials">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <header class="mb-space-lg">
            <div class="flex items-center gap-space-xs mb-space-2xs">
                <span class="px-space-xs py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm uppercase">Akademik</span>
                <span class="w-1 h-1 rounded-full bg-outline"></span>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Konten pembelajaran</span>
            </div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface">Manajemen Materi</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">Materi tersimpan di seluruh mata kuliah.</p>
        </header>

        @if (session('success'))
            <div class="mb-space-md rounded-xl border border-primary/20 bg-primary/10 px-space-md py-space-sm text-primary">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-space-md rounded-xl border border-error/20 bg-error/10 px-space-md py-space-sm text-error">Periksa kembali data yang dimasukkan.</div>
        @endif

        <section class="bg-surface-container-low p-space-sm rounded-2xl border border-outline/10 shadow-sm mb-space-md">
            <form method="GET" action="{{ route('admin.materials.index') }}" class="flex flex-col md:flex-row gap-space-sm">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari judul, deskripsi, atau mata kuliah..."
                    class="flex-1 min-w-0 bg-surface-container text-on-surface placeholder:text-outline border border-outline/10 rounded-xl px-space-md py-2.5 focus:outline-none focus:ring-2 focus:ring-primary">
                <select name="course_id" class="bg-surface-container text-on-surface border border-outline/10 rounded-xl px-space-md py-2.5 focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">Semua Mata Kuliah</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @selected((string) request('course_id') === (string) $course->id)>{{ $course->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-space-md py-2.5 rounded-xl bg-primary text-on-primary font-label-md hover:bg-primary-container transition-colors">Terapkan</button>
                @if (request()->filled('search') || request()->filled('course_id'))
                    <a href="{{ route('admin.materials.index') }}" class="px-space-sm py-2.5 rounded-xl bg-surface-container-high text-on-surface-variant hover:text-on-surface text-center">Reset</a>
                @endif
            </form>
        </section>

        <section class="bg-surface-container-low rounded-2xl border border-outline/10 shadow-md overflow-hidden" aria-label="Daftar materi">
            <div class="px-space-md py-space-sm border-b border-outline/10 flex items-center justify-between gap-space-sm">
                <div>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Daftar Materi</h2>
                    <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $materials->total() }} dari {{ $totalMaterials }} materi</span>
                </div>
                <a href="{{ route('admin.materials.create') }}" class="inline-flex items-center gap-space-xs rounded-xl bg-primary px-space-md py-2.5 font-label-md text-on-primary hover:bg-primary-container transition-colors">
                    <span class="material-symbols-outlined text-base">add</span>Tambah Materi
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left">
                    <thead class="bg-surface-container text-on-surface-variant">
                    <tr>
                        <th class="px-space-md py-space-sm font-label-sm text-label-sm uppercase">Mata Kuliah</th>
                        <th class="px-space-md py-space-sm font-label-sm text-label-sm uppercase">Judul Materi</th>
                        <th class="px-space-md py-space-sm font-label-sm text-label-sm uppercase">Jenis</th>
                        <th class="px-space-md py-space-sm font-label-sm text-label-sm uppercase">Diunggah Oleh</th>
                        <th class="px-space-md py-space-sm font-label-sm text-label-sm uppercase">Tanggal</th>
                        <th class="px-space-md py-space-sm font-label-sm text-label-sm uppercase text-right">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-outline/10">
                        @forelse ($materials as $material)
                            <tr class="hover:bg-surface-container transition-colors">
                                <td class="px-space-md py-space-sm">
                                    <span class="font-label-md text-label-md text-on-surface">{{ $material->course?->name ?? 'Mata kuliah tidak ditemukan' }}</span>
                                    <span class="block font-label-sm text-label-sm text-outline">{{ $material->course?->code ?? '—' }}</span>
                                </td>
                                <td class="px-space-md py-space-sm">
                                    <span class="font-label-md text-label-md text-on-surface">{{ $material->title }}</span>
                                    <span class="block max-w-md truncate font-body-sm text-body-sm text-on-surface-variant">{{ $material->description }}</span>
                                </td>
                                <td class="px-space-md py-space-sm">
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-primary/10 text-primary font-label-sm text-label-sm">{{ $material->type === 'link' ? 'Link' : 'File' }}</span>
                                </td>
                                <td class="px-space-md py-space-sm font-body-sm text-body-sm text-on-surface">{{ $material->uploader?->name ?? '—' }}</td>
                                <td class="px-space-md py-space-sm font-body-sm text-body-sm text-on-surface-variant">{{ $material->created_at?->format('d M Y') ?? '—' }}</td>
                                <td class="px-space-md py-space-sm">
                                    <div class="flex items-center justify-end gap-space-xs">
                                        @if ($material->type === 'file' && $material->file_path && $material->course)
                                            <a href="{{ route('courses.materials.download', [$material->course, $material]) }}" class="inline-flex items-center gap-1 rounded-lg bg-surface-container-high px-3 py-2 font-label-sm text-on-surface hover:text-primary transition-colors">
                                                <span class="material-symbols-outlined text-sm">download</span>Unduh
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.materials.edit', $material) }}" class="inline-flex items-center gap-1 rounded-lg bg-surface-container-high px-3 py-2 font-label-sm text-on-surface hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined text-sm">edit</span>Edit
                                        </a>
                                        <form action="{{ route('admin.materials.destroy', $material) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-error/10 px-3 py-2 font-label-sm text-error hover:bg-error/20 transition-colors">
                                                <span class="material-symbols-outlined text-sm">delete</span>Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-space-md py-space-xl text-center text-on-surface-variant">{{ request()->filled('search') || request()->filled('course_id') ? 'Tidak ada materi yang cocok dengan filter.' : 'Belum ada materi di database. Gunakan Tambah Materi untuk membuat data pertama.' }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($materials->hasPages())
                <div class="px-space-md py-space-sm border-t border-outline/10 flex justify-center">
                    {{ $materials->links('vendor.pagination.custom-pagination') }}
                </div>
            @endif
        </section>
    </div>
</x-layout>