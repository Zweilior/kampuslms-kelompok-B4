<x-layout title="Tugas dan Nilai - Dosen" active-nav="courses">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
         x-data="{ deleteOpen: false, deleteAction: '', deleteTitle: '' }"
         @keydown.escape.window="deleteOpen = false">
        <header class="mb-space-lg">
            <a href="{{ route('dosen.courses.show', ['course' => $course, 'focus' => 'assignments']) }}" class="mb-space-md inline-flex items-center gap-space-xs font-label-md text-on-surface-variant hover:text-primary">
                <span class="material-symbols-outlined text-base">arrow_back</span>{{ $course->name }}
            </a>
            <div class="flex flex-wrap items-end justify-between gap-space-md">
                <div>
                    <div class="flex items-center gap-space-xs mb-space-2xs">
                        <span class="px-space-xs py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm uppercase">Pengelolaan kelas</span>
                        <span class="w-1 h-1 rounded-full bg-outline"></span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $course->code }}</span>
                    </div>
                    <h1 class="font-headline-lg text-headline-lg text-on-surface">Tugas dan Nilai</h1>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">Pantau pengumpulan dan kelola nilai tugas mata kuliah ini.</p>
                </div>
                <a href="{{ route('dosen.courses.assignments.create', $course) }}" class="dosen-assignment__create">
                    <span class="material-symbols-outlined text-base">add</span>Buat Tugas
                </a>
            </div>
        </header>

        @if (session('success'))
            <div role="status" class="mb-space-md rounded-xl bg-primary/10 p-space-md font-body-sm text-body-sm text-primary">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div role="alert" class="mb-space-md rounded-xl bg-error-container p-space-md font-body-sm text-body-sm text-on-error-container">{{ session('error') }}</div>
        @endif

        <section aria-label="Daftar tugas mata kuliah" class="overflow-hidden rounded-2xl border border-outline/10 bg-surface-container-low shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-outline/10">
                    <thead class="bg-surface-container">
                        <tr>
                            <th scope="col" class="px-space-md py-space-sm text-left font-label-sm text-label-sm uppercase text-on-surface-variant">No.</th>
                            <th scope="col" class="px-space-md py-space-sm text-left font-label-sm text-label-sm uppercase text-on-surface-variant">Nama Tugas</th>
                            <th scope="col" class="px-space-md py-space-sm text-left font-label-sm text-label-sm uppercase text-on-surface-variant">Tenggat</th>
                            <th scope="col" class="px-space-md py-space-sm text-left font-label-sm text-label-sm uppercase text-on-surface-variant">Terkumpul</th>
                            <th scope="col" class="px-space-md py-space-sm text-left font-label-sm text-label-sm uppercase text-on-surface-variant">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline/10">
                        @forelse ($assignments as $assignment)
                            <tr>
                                <td class="whitespace-nowrap px-space-md py-space-md font-body-sm text-body-sm text-on-surface">{{ $loop->iteration }}</td>
                                <td class="min-w-48 px-space-md py-space-md">
                                    <p class="font-label-md text-label-md font-semibold text-on-surface">{{ $assignment->title }}</p>
                                    <p class="mt-0.5 font-body-sm text-body-sm text-on-surface-variant">
                                        {{ $assignment->status === 'published' ? 'Terbit' : 'Draft' }}
                                    </p>
                                </td>
                                <td class="whitespace-nowrap px-space-md py-space-md font-body-sm text-body-sm text-on-surface">
                                    {{ $assignment->due_at->format('d M Y, H:i') }}
                                </td>
                                <td class="whitespace-nowrap px-space-md py-space-md font-body-sm text-body-sm text-on-surface">
                                    {{ $assignment->submissions_count }} / {{ $studentCount }} mahasiswa
                                </td>
                                <td class="px-space-md py-space-md">
                                    <div class="dosen-assignment__actions">
                                        <a href="{{ route('dosen.courses.assignments.edit', [$course, $assignment]) }}" class="dosen-assignment__btn dosen-assignment__btn--edit">
                                            <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            Edit
                                        </a>
                                        @if ($assignment->due_at->lte(now()))
                                            <a href="{{ route('dosen.courses.assignments.submissions.index', [$course, $assignment]) }}" class="dosen-assignment__btn dosen-assignment__btn--grade">
                                                <span class="material-symbols-outlined" aria-hidden="true">grading</span>
                                                Edit Nilai
                                            </a>
                                        @endif
                                        <form action="{{ route('dosen.courses.assignments.destroy', [$course, $assignment]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                    class="dosen-assignment__btn dosen-assignment__btn--danger"
                                                    data-delete-url="{{ route('dosen.courses.assignments.destroy', [$course, $assignment]) }}"
                                                    data-assignment-title="{{ $assignment->title }}"
                                                    aria-haspopup="dialog"
                                                    @click="deleteAction = $el.dataset.deleteUrl; deleteTitle = $el.dataset.assignmentTitle; deleteOpen = true">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-space-md py-space-lg text-center font-body-md text-body-md text-on-surface-variant">Belum ada tugas untuk mata kuliah ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <template x-teleport="body">
            <div class="logout-dialog-backdrop"
                 x-cloak
                 x-show="deleteOpen"
                 x-transition.opacity
                 @click.self="deleteOpen = false"
                 role="presentation">
                <section class="logout-dialog" role="dialog" aria-modal="true" aria-labelledby="delete-assignment-title" @click.stop>
                    <div class="logout-dialog__icon" aria-hidden="true">
                        <span class="material-symbols-outlined">delete</span>
                    </div>
                    <h2 class="logout-dialog__title" id="delete-assignment-title">Hapus tugas ini?</h2>
                    <p class="logout-dialog__message">
                        Tugas <strong x-text="deleteTitle"></strong> akan dihapus. Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <div class="logout-dialog__actions">
                        <button type="button" class="logout-dialog__button" @click="deleteOpen = false">Batal</button>
                        <form method="POST" :action="deleteAction">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="logout-dialog__button logout-dialog__button--confirm">Ya, hapus tugas</button>
                        </form>
                    </div>
                </section>
            </div>
        </template>
    </div>
</x-layout>
