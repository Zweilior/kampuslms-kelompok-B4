<x-layout title="Manajemen Mata Kuliah">
    <div class="admin-courses">
        <div class="admin-courses__card">

            {{-- Header --}}
            <div class="admin-courses__header">
                <div class="admin-courses__header-left">
                    <span class="material-symbols-outlined admin-courses__header-icon">menu_book</span>
                    <h1 class="admin-courses__title">Semua Mata Kuliah</h1>
                </div>

                <div class="admin-courses__header-right">
                    <a href="{{ route('admin.courses.create') }}" class="admin-courses__btn-add">
                        <span class="material-symbols-outlined">add</span>
                        Tambah MK
                    </a>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="admin-courses__table-wrap">
                <table class="admin-courses__table">
                    <thead>
                        <tr>
                            <th>KODE</th>
                            <th>NAMA MK</th>
                            <th>SKS</th>
                            <th>DOSEN</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($courses ?? [
                            (object)['id' => 1, 'code' => 'IF-2023', 'name' => 'Pemrograman Web', 'sks' => 3, 'dosen' => 'Budi Santoso'],
                            (object)['id' => 2, 'code' => 'IF-2024', 'name' => 'Basis Data', 'sks' => 3, 'dosen' => 'Siti Aminah'],
                            (object)['id' => 3, 'code' => 'IF-2025', 'name' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'dosen' => 'Tara Schuppe PhD'],
                        ] as $course)
                        <tr>
                            <td class="admin-courses__code">{{ $course->code }}</td>
                            <td class="admin-courses__name">{{ $course->name }}</td>
                            <td class="admin-courses__sks">{{ $course->sks }}</td>
                            <td class="admin-courses__dosen">{{ $course->lecturer?->name ?? 'Belum ditentukan' }}</td>
                            <td class="admin-courses__actions">
                                <a href="{{ route('admin.courses.show', $course->id) }}" class="admin-courses__btn admin-courses__btn--detail">
                                    <span class="material-symbols-outlined">visibility</span>
                                    Kelola
                                </a>
                                <a href="{{ route('admin.courses.edit', $course->id) }}" class="admin-courses__btn admin-courses__btn--edit">
                                    <span class="material-symbols-outlined">edit</span>
                                    Edit
                                </a>
                                <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="admin-courses__btn admin-courses__btn--danger" onclick="return confirm('Hapus mata kuliah ini?')">
                                        <span class="material-symbols-outlined">delete</span>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="admin-courses__empty">Belum ada mata kuliah.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-layout>