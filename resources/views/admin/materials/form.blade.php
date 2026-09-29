<x-layout title="{{ $material ? 'Edit Materi' : 'Tambah Materi' }} - Admin" active-nav="materials">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <header class="mb-space-lg">
            <a href="{{ route('admin.materials.index') }}" class="inline-flex items-center gap-space-xs text-on-surface-variant hover:text-primary font-label-md mb-space-sm">
                <span class="material-symbols-outlined text-base">arrow_back</span>Kembali ke Materi
            </a>
            <h1 class="font-headline-lg text-headline-lg text-on-surface">{{ $material ? 'Edit Materi' : 'Tambah Materi' }}</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">Isi informasi materi untuk mata kuliah yang dipilih.</p>
        </header>

        <form action="{{ $material ? route('admin.materials.update', $material) : route('admin.materials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-space-md rounded-2xl border border-outline/10 bg-surface-container-low p-space-lg shadow-md">
            @csrf
            @if ($material)
                @method('PUT')
            @endif

            <div>
                <label for="course_id" class="mb-1 block font-label-md text-label-md text-on-surface">Mata Kuliah</label>
                <select id="course_id" name="course_id" required class="w-full rounded-xl border border-outline/10 bg-surface-container px-space-md py-3 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">Pilih mata kuliah</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" @selected((string) old('course_id', $material?->course_id) === (string) $course->id)>{{ $course->name }}</option>
                    @endforeach
                </select>
                @error('course_id') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="title" class="mb-1 block font-label-md text-label-md text-on-surface">Judul</label>
                <input id="title" name="title" value="{{ old('title', $material?->title) }}" required maxlength="255" class="w-full rounded-xl border border-outline/10 bg-surface-container px-space-md py-3 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                @error('title') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="mb-1 block font-label-md text-label-md text-on-surface">Deskripsi</label>
                <textarea id="description" name="description" rows="4" required class="w-full rounded-xl border border-outline/10 bg-surface-container px-space-md py-3 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">{{ old('description', $material?->description) }}</textarea>
                @error('description') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="type" class="mb-1 block font-label-md text-label-md text-on-surface">Jenis Materi</label>
                <select id="type" name="type" required class="w-full rounded-xl border border-outline/10 bg-surface-container px-space-md py-3 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="link" @selected(old('type', $material?->type ?? 'link') === 'link')>Link</option>
                    <option value="file" @selected(old('type', $material?->type) === 'file')>File</option>
                </select>
                @error('type') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="external_url" class="mb-1 block font-label-md text-label-md text-on-surface">URL Materi</label>
                <input id="external_url" type="url" name="external_url" value="{{ old('external_url', $material?->external_url) }}" placeholder="https://..." class="w-full rounded-xl border border-outline/10 bg-surface-container px-space-md py-3 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                @error('external_url') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="file" class="mb-1 block font-label-md text-label-md text-on-surface">File Materi</label>
                @if ($material?->original_name)
                    <p class="mb-2 font-body-sm text-body-sm text-on-surface-variant">File saat ini: {{ $material->original_name }}</p>
                @endif
                <input id="file" type="file" name="file" class="w-full rounded-xl border border-outline/10 bg-surface-container px-space-md py-3 text-on-surface file:mr-3 file:rounded-lg file:border-0 file:bg-primary file:px-3 file:py-2 file:text-on-primary">
                <p class="mt-1 text-sm text-on-surface-variant">Wajib diisi untuk jenis File; maksimal 20 MB. Saat mengedit, file lama tetap digunakan jika tidak memilih file baru.</p>
                @error('file') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-space-sm border-t border-outline/10 pt-space-md">
                <a href="{{ route('admin.materials.index') }}" class="rounded-xl bg-surface-container-high px-space-md py-2.5 font-label-md text-on-surface">Batal</a>
                <button type="submit" class="rounded-xl bg-primary px-space-md py-2.5 font-label-md text-on-primary hover:bg-primary-container">{{ $material ? 'Simpan Perubahan' : 'Simpan Materi' }}</button>
            </div>
        </form>
    </div>
</x-layout>