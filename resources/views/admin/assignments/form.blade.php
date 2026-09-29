<x-layout title="{{ $assignment ? 'Edit Tugas' : 'Tambah Tugas' }} - Admin" active-nav="assignments">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <header class="mb-space-lg">
            <a href="{{ route('admin.assignments.index') }}" class="inline-flex items-center gap-space-xs text-on-surface-variant hover:text-primary font-label-md mb-space-sm">
                <span class="material-symbols-outlined text-base">arrow_back</span>Kembali ke Tugas
            </a>
            <h1 class="font-headline-lg text-headline-lg text-on-surface">{{ $assignment ? 'Edit Tugas' : 'Tambah Tugas' }}</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">Atur tugas, mata kuliah, dan batas pengumpulan.</p>
        </header>

        <form action="{{ $assignment ? route('admin.assignments.update', $assignment) : route('admin.assignments.store') }}" method="POST" class="space-y-space-md rounded-2xl border border-outline/10 bg-surface-container-low p-space-lg shadow-md">
            @csrf
            @if ($assignment)
                @method('PUT')
            @endif

            <div>
                <label for="course_id" class="mb-1 block font-label-md text-label-md text-on-surface">Mata Kuliah</label>
                <select id="course_id" name="course_id" required class="w-full rounded-xl border border-outline/10 bg-surface-container px-space-md py-3 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">Pilih mata kuliah</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @selected((string) old('course_id', $assignment?->course_id) === (string) $course->id)>{{ $course->name }}</option>
                    @endforeach
                </select>
                @error('course_id') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="title" class="mb-1 block font-label-md text-label-md text-on-surface">Judul Tugas</label>
                <input id="title" name="title" value="{{ old('title', $assignment?->title) }}" required maxlength="255" class="w-full rounded-xl border border-outline/10 bg-surface-container px-space-md py-3 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                @error('title') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="instructions" class="mb-1 block font-label-md text-label-md text-on-surface">Instruksi</label>
                <textarea id="instructions" name="instructions" rows="5" required class="w-full rounded-xl border border-outline/10 bg-surface-container px-space-md py-3 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">{{ old('instructions', $assignment?->instructions) }}</textarea>
                @error('instructions') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                <div>
                    <label for="due_at" class="mb-1 block font-label-md text-label-md text-on-surface">Deadline</label>
                    <input id="due_at" type="datetime-local" name="due_at" value="{{ old('due_at', $assignment?->due_at?->format('Y-m-d\TH:i')) }}" required class="w-full rounded-xl border border-outline/10 bg-surface-container px-space-md py-3 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                    @error('due_at') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="max_score" class="mb-1 block font-label-md text-label-md text-on-surface">Nilai Maksimal</label>
                    <input id="max_score" type="number" name="max_score" min="0" max="255" value="{{ old('max_score', $assignment?->max_score ?? 100) }}" required class="w-full rounded-xl border border-outline/10 bg-surface-container px-space-md py-3 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                    @error('max_score') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="mb-1 block font-label-md text-label-md text-on-surface">Status</label>
                    <select id="status" name="status" required class="w-full rounded-xl border border-outline/10 bg-surface-container px-space-md py-3 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="draft" @selected(old('status', $assignment?->status ?? 'draft') === 'draft')>Draft</option>
                        <option value="published" @selected(old('status', $assignment?->status) === 'published')>Published</option>
                    </select>
                    @error('status') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="allow_late" class="mb-1 block font-label-md text-label-md text-on-surface">Pengumpulan Terlambat</label>
                    <select id="allow_late" name="allow_late" required class="w-full rounded-xl border border-outline/10 bg-surface-container px-space-md py-3 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="1" @selected((string) old('allow_late', (int) ($assignment?->allow_late ?? true)) === '1')>Diizinkan</option>
                        <option value="0" @selected((string) old('allow_late', (int) ($assignment?->allow_late ?? true)) === '0')>Tidak diizinkan</option>
                    </select>
                    @error('allow_late') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-space-sm border-t border-outline/10 pt-space-md">
                <a href="{{ route('admin.assignments.index') }}" class="rounded-xl bg-surface-container-high px-space-md py-2.5 font-label-md text-on-surface">Batal</a>
                <button type="submit" class="rounded-xl bg-primary px-space-md py-2.5 font-label-md text-on-primary hover:bg-primary-container">{{ $assignment ? 'Simpan Perubahan' : 'Simpan Tugas' }}</button>
            </div>
        </form>
    </div>
</x-layout>