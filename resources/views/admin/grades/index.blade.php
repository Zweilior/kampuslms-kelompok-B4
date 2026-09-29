<x-layout title="Monitoring Nilai - Admin" active-nav="nilai">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <header class="mb-space-lg">
            <div class="flex items-center gap-space-xs mb-space-2xs">
                <span class="px-space-xs py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm uppercase">
                    Akademik
                </span>
                <span class="w-1 h-1 rounded-full bg-outline"></span>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Rekap penilaian</span>
            </div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface">Monitoring Nilai</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">
                Pantau nilai submission mahasiswa di seluruh mata kuliah.
            </p>
        </header>

        <section aria-label="Ringkasan nilai" class="grid grid-cols-1 sm:grid-cols-2 gap-bento-gap-desktop mb-space-lg">
            <article class="bg-surface-container-low p-space-lg rounded-2xl shadow-md border border-outline/10">
                <div class="flex items-center gap-space-xs">
                    <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-headline-sm">grading</span>
                    </div>
                    <span class="font-label-lg text-label-lg text-on-surface-variant">Submission Dinilai</span>
                </div>
                <p class="mt-space-md font-display-lg text-display-lg text-on-surface font-bold leading-none">{{ number_format($totalGrades) }}</p>
                <p class="mt-space-xs font-label-sm text-label-sm text-outline">Total nilai tersimpan</p>
            </article>

            <article class="bg-surface-container-low p-space-lg rounded-2xl shadow-md border border-outline/10">
                <div class="flex items-center gap-space-xs">
                    <div class="w-9 h-9 rounded-xl bg-secondary-container flex items-center justify-center text-on-secondary-container">
                        <span class="material-symbols-outlined text-headline-sm">analytics</span>
                    </div>
                    <span class="font-label-lg text-label-lg text-on-surface-variant">Rata-rata Nilai</span>
                </div>
                <p class="mt-space-md font-display-lg text-display-lg text-on-surface font-bold leading-none">{{ number_format((float) $averageScore, 1) }}</p>
                <p class="mt-space-xs font-label-sm text-label-sm text-outline">Dari seluruh submission yang dinilai</p>
            </article>
        </section>

        <section class="bg-surface-container-low rounded-2xl shadow-md border border-outline/10 overflow-hidden" aria-label="Daftar nilai mahasiswa">
            <div class="px-space-md py-space-sm border-b border-outline/10 flex items-center justify-between gap-space-sm">
                <h2 class="font-headline-sm text-headline-sm text-on-surface">Daftar Nilai</h2>
                <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $grades->total() }} data</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left">
                    <thead class="bg-surface-container text-on-surface-variant">
                    <tr>
                        <th class="px-space-md py-space-sm font-label-sm text-label-sm uppercase">Mata Kuliah</th>
                        <th class="px-space-md py-space-sm font-label-sm text-label-sm uppercase">Tugas</th>
                        <th class="px-space-md py-space-sm font-label-sm text-label-sm uppercase">Mahasiswa</th>
                        <th class="px-space-md py-space-sm font-label-sm text-label-sm uppercase">Nilai</th>
                        <th class="px-space-md py-space-sm font-label-sm text-label-sm uppercase">Dinilai Oleh</th>
                        <th class="px-space-md py-space-sm font-label-sm text-label-sm uppercase">Tanggal</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-outline/10">
                        @forelse ($grades as $grade)
                            @php
                                $submission = $grade->submission;
                                $assignment = $submission?->assignment;
                            @endphp
                            <tr class="hover:bg-surface-container transition-colors">
                                <td class="px-space-md py-space-sm">
                                    <span class="font-label-md text-label-md text-on-surface">{{ $assignment?->course?->name ?? 'Mata kuliah tidak ditemukan' }}</span>
                                    @if ($assignment?->course?->code)
                                        <span class="block font-label-sm text-label-sm text-outline">{{ $assignment->course->code }}</span>
                                    @endif
                                </td>
                                <td class="px-space-md py-space-sm font-body-sm text-body-sm text-on-surface">{{ $assignment?->title ?? 'Tugas tidak ditemukan' }}</td>
                                <td class="px-space-md py-space-sm">
                                    <span class="font-label-md text-label-md text-on-surface">{{ $submission?->student?->name ?? 'Mahasiswa tidak ditemukan' }}</span>
                                    <span class="block font-label-sm text-label-sm text-outline">{{ $submission?->student?->nim_nip ?? '—' }}</span>
                                </td>
                                <td class="px-space-md py-space-sm">
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-primary/10 text-primary font-label-md text-label-md font-bold">
                                        {{ number_format((float) $grade->score, 2) }} / {{ $assignment?->max_score ?? 100 }}
                                    </span>
                                </td>
                                <td class="px-space-md py-space-sm font-body-sm text-body-sm text-on-surface">{{ $grade->grader?->name ?? '—' }}</td>
                                <td class="px-space-md py-space-sm font-body-sm text-body-sm text-on-surface-variant">{{ $grade->graded_at?->format('d M Y') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-space-md py-space-xl text-center text-on-surface-variant">
                                    Belum ada nilai submission yang tersimpan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($grades->hasPages())
                <div class="px-space-md py-space-sm border-t border-outline/10 flex justify-center">
                    {{ $grades->links('vendor.pagination.custom-pagination') }}
                </div>
            @endif
        </section>

    </div>
</x-layout>