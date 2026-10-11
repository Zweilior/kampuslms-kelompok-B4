<x-layout title="Dashboard Dosen - KampusLMS" active-nav="dashboard">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <header class="dosen-dashboard__header lms-page-header">
            <div class="dosen-dashboard__header-copy">
                <h1 class="dosen-dashboard__title">Halo, {{ auth()->user()->name }}</h1>
                <p class="dosen-dashboard__description">Aktivitas dan penilaian untuk mata kuliah yang Anda ampu.</p>
            </div>
            <a href="{{ route('dosen.courses.index') }}" class="dosen-dashboard__action">
                <span class="material-symbols-outlined" aria-hidden="true">menu_book</span>
                Mata kuliah saya
            </a>
        </header>

        <section aria-label="Ringkasan aktivitas dosen" class="grid grid-cols-1 md:grid-cols-3 gap-bento-gap-desktop">
            <a href="{{ route('dosen.courses.index') }}" class="group rounded-2xl border border-outline/10 bg-surface-container-low p-space-lg shadow-sm transition-colors hover:bg-surface-container">
                <span class="mb-space-md flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary">
                    <span class="material-symbols-outlined">menu_book</span>
                </span>
                <h2 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary">Mata Kuliah</h2>
                <p class="mt-space-xs font-body-sm text-body-sm text-on-surface-variant">Jumlah mata kuliah yang Anda ampu.</p>
                <span class="mt-space-md block font-headline-lg text-headline-lg text-primary">{{ $totalCourses }}</span>
                <span class="mt-space-sm inline-flex items-center gap-1 font-label-md text-label-md text-primary">Lihat mata kuliah <span class="material-symbols-outlined text-base">arrow_forward</span></span>
            </a>

            <a href="{{ route('dosen.courses.index', ['focus' => 'assignments']) }}" class="group rounded-2xl border border-outline/10 bg-surface-container-low p-space-lg shadow-sm transition-colors hover:bg-surface-container">
                <span class="mb-space-md flex h-11 w-11 items-center justify-center rounded-xl bg-secondary-container text-on-secondary-container">
                    <span class="material-symbols-outlined">assignment</span>
                </span>
                <h2 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary">Tugas Dibuka</h2>
                <p class="mt-space-xs font-body-sm text-body-sm text-on-surface-variant">Tugas terbit yang masih dalam masa pengumpulan.</p>
                <span class="mt-space-md block font-headline-lg text-headline-lg text-primary">{{ $openAssignments }}</span>
                <span class="mt-space-sm inline-flex items-center gap-1 font-label-md text-label-md text-primary">Lihat tugas <span class="material-symbols-outlined text-base">arrow_forward</span></span>
            </a>

            <a href="#grading-assignments" class="group rounded-2xl border border-outline/10 bg-surface-container-low p-space-lg shadow-sm transition-colors hover:bg-surface-container">
                <span class="mb-space-md flex h-11 w-11 items-center justify-center rounded-xl bg-tertiary-container/30 text-tertiary">
                    <span class="material-symbols-outlined">grading</span>
                </span>
                <h2 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary">Perlu Dinilai</h2>
                <p class="mt-space-xs font-body-sm text-body-sm text-on-surface-variant">Pengumpulan setelah tenggat yang belum dinilai.</p>
                <span class="mt-space-md block font-headline-lg text-headline-lg text-primary">{{ $ungradedSubmissions }}</span>
                <span class="mt-space-sm inline-flex items-center gap-1 font-label-md text-label-md text-primary">Lihat tugas <span class="material-symbols-outlined text-base">arrow_forward</span></span>
            </a>
        </section>

        <section id="grading-assignments" class="mt-space-lg overflow-hidden rounded-2xl border border-outline/10 bg-surface-container-low shadow-sm" aria-label="Tugas yang perlu dinilai">
            <header class="flex flex-wrap items-center justify-between gap-space-sm border-b border-outline/10 px-space-lg py-space-md">
                <div>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Tugas Perlu Dinilai</h2>
                    <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Tugas yang tenggatnya selesai dan masih memiliki pengumpulan tanpa nilai.</p>
                </div>
                <a href="{{ route('dosen.grades.index') }}" class="font-label-md text-label-md text-primary hover:underline">Monitoring nilai</a>
            </header>
            <div class="divide-y divide-outline/10">
                @forelse ($gradingAssignments as $assignment)
                    <a href="{{ route('dosen.courses.assignments.submissions.index', [$assignment->course, $assignment]) }}"
                       class="flex flex-wrap items-center justify-between gap-space-sm px-space-lg py-space-md transition-colors hover:bg-surface-container">
                        <span>
                            <strong class="block font-label-lg text-label-lg text-on-surface">{{ $assignment->title }}</strong>
                            <span class="mt-1 block font-body-sm text-body-sm text-on-surface-variant">{{ $assignment->course->code }} · {{ $assignment->course->name }}</span>
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-tertiary-container/30 px-space-sm py-1 font-label-sm text-label-sm text-tertiary">
                            {{ $assignment->ungraded_submissions_count }} belum dinilai
                        </span>
                    </a>
                @empty
                    <p class="px-space-lg py-space-lg font-body-sm text-body-sm text-on-surface-variant">Tidak ada pengumpulan yang menunggu penilaian.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-layout>