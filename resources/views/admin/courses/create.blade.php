<x-layout title="Tambah Mata Kuliah">
    <div class="admin-course-form mx-auto max-w-[640px] px-4">
        <div class="admin-course-form__card rounded-[var(--radius-lg)] border border-[var(--border-hairline)] bg-[var(--bg-elevated)] p-7 shadow-[var(--shadow-md)]">
            <h1 class="admin-course-form__title m-0 mb-6 text-2xl font-bold tracking-[-0.02em] text-[var(--text-primary)]">Tambah Mata Kuliah</h1>

            <form action="{{ route('admin.courses.store') }}" method="POST">
                @include('admin.courses._form')

                <div class="admin-course-form__actions mt-7 flex items-center justify-end gap-3 border-t border-[var(--border-hairline)] pt-6">
                    <a href="{{ route('admin.courses.index') }}" class="btn btn--ghost">Batal</a>
                    <button type="submit" class="btn btn--primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
