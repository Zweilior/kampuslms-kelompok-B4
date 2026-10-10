<x-layout title="Rubrik Penilaian - Dosen" active-nav="courses">
    <main class="dosen-rubric"
          x-data="{ deleteOpen: false, deleteAction: '', deleteName: '' }"
          @keydown.escape.window="deleteOpen = false">
        <a href="{{ route('dosen.courses.show', ['course' => $course, 'focus' => 'materials']) }}" class="dosen-rubric__back">
            <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
            Kembali ke mata kuliah
        </a>

        <header class="dosen-rubric__header">
            <div>
                <div class="dosen-rubric__eyebrow">
                    <span>{{ $course->code }}</span>
                    <span aria-hidden="true"></span>
                    <span>Pengelolaan rubrik</span>
                </div>
                <h1>Rubrik Penilaian</h1>
                <p>Atur komponen dan bobot penilaian untuk {{ $course->name }}.</p>
            </div>
        </header>

        @if (session('success'))
            <div class="dosen-rubric__notice dosen-rubric__notice--success" role="status">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="dosen-rubric__notice dosen-rubric__notice--error" role="alert">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="dosen-rubric__notice dosen-rubric__notice--error" role="alert">
                <p>Periksa kembali isian rubrik:</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="dosen-rubric__panel" aria-labelledby="rubric-list-title">
            <div class="dosen-rubric__panel-heading">
                <div>
                    <h2 id="rubric-list-title">Komponen Penilaian</h2>
                    <p>Ubah nama dan bobot komponen langsung dari daftar ini.</p>
                </div>
                <span class="dosen-rubric__count">{{ $gradeComponents->count() }} komponen</span>
            </div>

            <div class="dosen-rubric__table-wrap">
                <table class="dosen-rubric__table">
                    <thead>
                        <tr>
                            <th scope="col">Komponen</th>
                            <th scope="col">Bobot (%)</th>
                            <th scope="col">Tugas</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($gradeComponents as $component)
                            <tr>
                                <td colspan="4">
                                    <form action="{{ route('dosen.courses.grade-components.update', [$course, $component]) }}"
                                          method="POST"
                                          class="dosen-rubric__row-form">
                                        @csrf
                                        @method('PUT')
                                        <label class="sr-only" for="component-name-{{ $component->id }}">Nama komponen</label>
                                        <input id="component-name-{{ $component->id }}" name="name" type="text" value="{{ $component->name }}" maxlength="255" required>
                                        <label class="sr-only" for="component-weight-{{ $component->id }}">Bobot dalam persen</label>
                                        <div class="dosen-rubric__weight">
                                            <input id="component-weight-{{ $component->id }}" name="weight" type="number" value="{{ $component->weight }}" min="0" max="100" step="0.01" required>
                                            <span>%</span>
                                        </div>
                                        <span class="dosen-rubric__assignment-count">{{ $component->assignments_count }}</span>
                                        <div class="dosen-rubric__actions">
                                            <button type="submit" class="dosen-rubric__button dosen-rubric__button--save">
                                                <span class="material-symbols-outlined" aria-hidden="true">save</span>
                                                Simpan
                                            </button>
                                            <button type="button"
                                                    class="dosen-rubric__button dosen-rubric__button--delete"
                                                    data-delete-url="{{ route('dosen.courses.grade-components.destroy', [$course, $component]) }}"
                                                    data-component-name="{{ $component->name }}"
                                                    @click="deleteAction = $el.dataset.deleteUrl; deleteName = $el.dataset.componentName; deleteOpen = true">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                                Hapus
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="dosen-rubric__empty">Belum ada komponen rubrik untuk mata kuliah ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="dosen-rubric__panel dosen-rubric__create" aria-labelledby="rubric-create-title">
            <div class="dosen-rubric__panel-heading">
                <div>
                    <h2 id="rubric-create-title">Tambah Komponen</h2>
                    <p>Tambahkan aspek penilaian beserta bobotnya.</p>
                </div>
            </div>
            <form action="{{ route('dosen.courses.grade-components.store', $course) }}" method="POST" class="dosen-rubric__create-form">
                @csrf
                <div>
                    <label for="new-component-name">Nama komponen</label>
                    <input id="new-component-name" name="name" type="text" value="{{ old('name') }}" maxlength="255" placeholder="Contoh: Ujian Tengah Semester" required>
                </div>
                <div>
                    <label for="new-component-weight">Bobot (%)</label>
                    <input id="new-component-weight" name="weight" type="number" value="{{ old('weight') }}" min="0" max="100" step="0.01" placeholder="Contoh: 30" required>
                </div>
                <button type="submit" class="dosen-rubric__button dosen-rubric__button--primary">
                    <span class="material-symbols-outlined" aria-hidden="true">add</span>
                    Tambah Komponen
                </button>
            </form>
        </section>

        <template x-teleport="body">
            <div class="logout-dialog-backdrop"
                 x-cloak
                 x-show="deleteOpen"
                 x-transition.opacity
                 @click.self="deleteOpen = false"
                 role="presentation">
                <section class="logout-dialog" role="dialog" aria-modal="true" aria-labelledby="delete-component-title" @click.stop>
                    <div class="logout-dialog__icon" aria-hidden="true">
                        <span class="material-symbols-outlined">delete</span>
                    </div>
                    <h2 class="logout-dialog__title" id="delete-component-title">Hapus komponen rubrik?</h2>
                    <p class="logout-dialog__message">Komponen <strong x-text="deleteName"></strong> akan dihapus. Komponen yang memiliki tugas terkait tidak dapat dihapus.</p>
                    <div class="logout-dialog__actions">
                        <button type="button" class="logout-dialog__button" @click="deleteOpen = false">Batal</button>
                        <form method="POST" :action="deleteAction">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="logout-dialog__button logout-dialog__button--confirm">Ya, hapus komponen</button>
                        </form>
                    </div>
                </section>
            </div>
        </template>
    </main>
</x-layout>
