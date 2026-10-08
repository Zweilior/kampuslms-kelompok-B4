<x-layout title="Materi - Dosen" active-nav="courses">
    <main class="dosen-materials" x-data="{ deleteOpen: false, deleteAction: '', deleteTitle: '' }" @keydown.escape.window="deleteOpen = false">
        <a href="{{ route('dosen.courses.show', ['course' => $course, 'focus' => 'materials']) }}" class="dosen-assignment-form__back">
            <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
            Kembali ke mata kuliah
        </a>

        <header class="dosen-materials__header">
            <div>
                <div class="dosen-assignment-form__eyebrow">
                    <span>{{ $course->code }}</span>
                    <span class="dosen-assignment-form__dot" aria-hidden="true"></span>
                    <span>Materi perkuliahan</span>
                </div>
                <h1>{{ $course->name }}</h1>
                <p>Kelola materi belajar dan sumber referensi untuk mata kuliah ini.</p>
            </div>
            <a href="{{ route('dosen.courses.materials.create', $course) }}" class="dosen-assignment__create">
                <span class="material-symbols-outlined" aria-hidden="true">upload_file</span>
                Upload Materi
            </a>
        </header>

        @if (session('success'))
            <div class="dosen-materials__notice" role="status">
                <span class="material-symbols-outlined" aria-hidden="true">check_circle</span>{{ session('success') }}
            </div>
        @endif

        <section class="dosen-material-list" aria-label="Daftar materi mata kuliah">
            @forelse ($materials as $material)
                <article class="dosen-material-card">
                    <span class="dosen-material-card__icon material-symbols-outlined" aria-hidden="true">
                        {{ $material->type === 'link' ? 'link' : 'description' }}
                    </span>
                    <div class="dosen-material-card__content">
                        <div class="dosen-material-card__title-row">
                            <h2>{{ $material->title }}</h2>
                            <span class="dosen-material-card__type">{{ $material->type === 'link' ? 'Tautan' : 'File' }}</span>
                        </div>
                        @if ($material->description)
                            <p class="dosen-material-card__description">{{ $material->description }}</p>
                        @endif
                        @if ($material->type === 'link' && $material->external_url)
                            <a href="{{ $material->external_url }}" class="dosen-material-card__resource" target="_blank" rel="noopener">
                                <span class="material-symbols-outlined" aria-hidden="true">open_in_new</span>
                                Buka tautan materi
                            </a>
                        @elseif ($material->original_name)
                            <p class="dosen-material-card__file">
                                <span class="material-symbols-outlined" aria-hidden="true">attach_file</span>
                                {{ $material->original_name }}
                                @if ($material->file_size)
                                    <span>· {{ number_format($material->file_size / 1048576, 1) }} MB</span>
                                @endif
                            </p>
                        @endif
                    </div>
                    <div class="dosen-assignment__actions dosen-material-card__actions">
                        <a href="{{ route('dosen.courses.materials.edit', [$course, $material]) }}" class="dosen-assignment__btn dosen-assignment__btn--edit">
                            <span class="material-symbols-outlined" aria-hidden="true">edit</span>Edit
                        </a>
                        <form action="{{ route('dosen.courses.materials.destroy', [$course, $material]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                    class="dosen-assignment__btn dosen-assignment__btn--danger"
                                    data-delete-url="{{ route('dosen.courses.materials.destroy', [$course, $material]) }}"
                                    data-material-title="{{ $material->title }}"
                                    @click="deleteAction = $el.dataset.deleteUrl; deleteTitle = $el.dataset.materialTitle; deleteOpen = true">
                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>Hapus
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="dosen-materials__empty">
                    <span class="material-symbols-outlined" aria-hidden="true">auto_stories</span>
                    <h2>Belum ada materi</h2>
                    <p>Tambahkan file atau tautan referensi agar mahasiswa bisa mengakses materi kelas.</p>
                    <a href="{{ route('dosen.courses.materials.create', $course) }}" class="dosen-assignment__create">
                        <span class="material-symbols-outlined" aria-hidden="true">add</span>Tambah Materi
                    </a>
                </div>
            @endforelse
        </section>

        <template x-teleport="body">
            <div class="logout-dialog-backdrop" x-cloak x-show="deleteOpen" x-transition.opacity @click.self="deleteOpen = false" role="presentation">
                <section class="logout-dialog" role="dialog" aria-modal="true" aria-labelledby="delete-material-title" @click.stop>
                    <div class="logout-dialog__icon" aria-hidden="true">
                        <span class="material-symbols-outlined">delete</span>
                    </div>
                    <h2 class="logout-dialog__title" id="delete-material-title">Hapus materi ini?</h2>
                    <p class="logout-dialog__message">Materi <strong x-text="deleteTitle"></strong> akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
                    <div class="logout-dialog__actions">
                        <button type="button" class="logout-dialog__button" @click="deleteOpen = false">Batal</button>
                        <form method="POST" :action="deleteAction">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="logout-dialog__button logout-dialog__button--confirm">Ya, hapus materi</button>
                        </form>
                    </div>
                </section>
            </div>
        </template>
    </main>
</x-layout>
