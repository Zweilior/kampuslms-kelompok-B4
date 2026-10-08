<x-layout title="Mata Kuliah Diampu - Dosen" active-nav="courses">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @php
            $focus = request('focus') === 'assignments' ? 'assignments' : 'materials';
        @endphp
        <header class="mb-space-lg">
            <div class="flex items-center gap-space-xs mb-space-2xs">
                <span class="px-space-xs py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm uppercase">Dosen</span>
                <span class="w-1 h-1 rounded-full bg-outline"></span>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Daftar pengampu</span>
            </div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface">Mata Kuliah yang Anda Ampu</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">Pilih mata kuliah untuk melihat detail dan mengelola kelas.</p>
        </header>

        <section aria-label="Daftar mata kuliah diampu" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-bento-gap-desktop">
            @forelse ($courses as $course)
                <article class="rounded-2xl border border-outline/10 bg-surface-container-low p-space-lg shadow-md">
                    <div class="mb-space-md flex items-center justify-between gap-space-sm">
                        <span class="rounded-md bg-tertiary-container/30 px-2.5 py-1 font-label-md text-label-md font-bold text-tertiary">{{ $course->code }}</span>
                        <span class="rounded-full bg-primary/10 px-2.5 py-1 font-label-sm text-label-sm text-primary">{{ $course->sks }} SKS</span>
                    </div>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">{{ $course->name }}</h2>
                    <p class="mt-space-xs font-body-sm text-body-sm text-on-surface-variant">{{ $course->lecturer->name }}</p>
                    <a href="{{ route('dosen.courses.show', ['course' => $course, 'focus' => $focus]) }}" class="mt-space-md inline-flex w-full items-center justify-center gap-space-xs rounded-xl bg-primary px-space-md py-2.5 font-label-md text-on-primary hover:bg-primary-container">
                        Lihat mata kuliah <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </a>
                </article>
            @empty
                <p class="col-span-full rounded-2xl border border-outline/10 bg-surface-container-low p-space-lg font-body-md text-body-md text-on-surface-variant">
                    Saat ini Anda belum mengampu mata kuliah.
                </p>
            @endforelse
        </section>
    </div>
</x-layout>
