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

        <div class="mahasiswa-page__grade-weights" aria-label="Komposisi bobot penilaian">
            @foreach ($gradeCategories as $category)
                <span class="mahasiswa-page__grade-weight">
                    <span>{{ $category['label'] }}</span>
                    <strong>{{ $category['weight'] }}%</strong>
                </span>
            @endforeach
        </div>

        <section class="mahasiswa-page__card mahasiswa-page__table-wrap">
            <table class="mahasiswa-page__table mahasiswa-page__grade-detail-table">
                <thead>
                    <tr>
                        <th>Item penilaian</th>
                        <th>Bobot</th>
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
                                        <span>{{ $assignment->grade_category_label }}</span>
                                    </a>
                                @else
                                    <a href="{{ route('mahasiswa.courses.assignments.submissions.create', [$course->id, $assignment->id]) }}"
                                        class="mahasiswa-page__grade-course">
                                        <strong>{{ $assignment->title }}</strong>
                                        <span>{{ $assignment->grade_category_label }} · Belum dikumpulkan</span>
                                    </a>
                                @endif
                            </td>
                            <td>
                                <span class="mahasiswa-page__grade-weight-value">
                                    {{ $assignment->grade_category_weight }}%
                                </span>
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
                            <td colspan="3" class="mahasiswa-page__empty">Belum ada tugas yang dipublikasikan.</td>
                        </tr>
                    @endforelse
                    <tr class="mahasiswa-page__grade-summary">
                        <th>Total</th>
                        <td>100%</td>
                        <td>
                            @if ($finalScore !== null)
                                {{ number_format($finalScore, 2) }}
                                <span class="mahasiswa-page__pill mahasiswa-page__pill--success"
                                    aria-label="Predikat {{ $finalLetterGrade }}">
                                    {{ $finalLetterGrade }}
                                </span>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
@endsection
