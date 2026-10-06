@extends('components.layout')

@section('content')
    <div class="mahasiswa-page mahasiswa-page--grades">
        <header class="mahasiswa-page__header">
            <div class="mahasiswa-page__header-copy">
                <a href="{{ route('mahasiswa.courses.show', $course->id) }}" class="mahasiswa-page__back">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Kembali ke kelas
                </a>
                <h1 class="mahasiswa-page__title">Nilai Tugas</h1>
                <p class="mahasiswa-page__description">{{ $course->name }} · Rincian nilai dan bobot penilaian.</p>
            </div>
            <span class="mahasiswa-page__pill mahasiswa-page__pill--code">{{ $course->code }}</span>
        </header>

        <section class="mahasiswa-page__card mahasiswa-page__table-wrap">
            <table class="mahasiswa-page__table mahasiswa-page__grade-detail-table">
                <thead>
                    <tr>
                        <th>Item penilaian</th>
                        <th>Bobot</th>
                        <th>Terhitung</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assignments as $assignment)
                        @php($submission = $assignment->submissions->first())
                        <tr>
                            <td>
                                @if ($submission)
                                    <a href="{{ route('mahasiswa.courses.assignments.submissions.index', [$course->id, $assignment->id]) }}"
                                        class="mahasiswa-page__grade-course">
                                        <strong>{{ $assignment->title }}</strong>
                                        <span>{{ $assignment->gradeComponent?->name ?? 'Lihat detail nilai' }}</span>
                                    </a>
                                @else
                                    <a href="{{ route('mahasiswa.courses.assignments.submissions.create', [$course->id, $assignment->id]) }}"
                                        class="mahasiswa-page__grade-course">
                                        <strong>{{ $assignment->title }}</strong>
                                        <span>{{ $assignment->gradeComponent?->name ?? 'Belum dikumpulkan · Kumpulkan tugas' }}</span>
                                    </a>
                                @endif
                            </td>
                            <td>
                                {{ $assignment->gradeComponent ? number_format((float) $assignment->gradeComponent->weight, 2) . '%' : '—' }}
                            </td>
                            <td>
                                @if ($submission?->grade && $assignment->gradeComponent)
                                    @php($weightedScore = ((float) $submission->grade->score / max($assignment->max_score, 1)) * (float) $assignment->gradeComponent->weight)
                                    {{ number_format($weightedScore, 2) }}
                                @elseif ($submission)
                                    <span class="mahasiswa-page__pill">Menunggu nilai</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if ($submission?->grade)
                                    {{ number_format((float) $submission->grade->score, 2) }}
                                    <span class="mahasiswa-page__table-subtext">dari {{ $assignment->max_score }}</span>
                                @elseif ($submission)
                                    <span class="mahasiswa-page__pill">Belum dinilai</span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="mahasiswa-page__empty">Belum ada tugas yang dipublikasikan.</td>
                        </tr>
                    @endforelse
                    <tr class="mahasiswa-page__grade-summary">
                        <th>Total</th>
                        <td>{{ number_format((float) $totalWeight, 2) }}%</td>
                        <td>—</td>
                        <td>{{ $finalGrade ? number_format((float) $finalGrade->total_score, 2) : '—' }}</td>
                    </tr>
                    <tr class="mahasiswa-page__grade-summary">
                        <th colspan="3">Predikat</th>
                        <td>{{ $finalGrade?->letter_grade ?? '—' }}</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
@endsection
