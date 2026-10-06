@extends('components.layout')

@section('content')
    <div class="mahasiswa-page mahasiswa-page--grades-index">
        <header class="mahasiswa-page__header">
            <div class="mahasiswa-page__header-copy">
                <h1 class="mahasiswa-page__title">Monitoring Nilai</h1>
                <p class="mahasiswa-page__description">Pantau nilai dan predikat dari mata kuliah yang kamu ikuti.</p>
            </div>
        </header>

        <section class="mahasiswa-page__card mahasiswa-page__grade-panel">
            <div class="mahasiswa-page__grade-tabs" aria-label="Status mata kuliah">
                <a href="{{ route('mahasiswa.grades.index', ['status' => 'active']) }}"
                    class="mahasiswa-page__grade-tab {{ $status === 'active' ? 'is-active' : '' }}"
                    @if ($status === 'active') aria-current="page" @endif>
                    Mata Kuliah Aktif <span>{{ $activeCourseCount }}</span>
                </a>
                <a href="{{ route('mahasiswa.grades.index', ['status' => 'archived']) }}"
                    class="mahasiswa-page__grade-tab {{ $status === 'archived' ? 'is-active' : '' }}"
                    @if ($status === 'archived') aria-current="page" @endif>
                    Mata Kuliah Arsip <span>{{ $archivedCourseCount }}</span>
                </a>
            </div>

            <div class="mahasiswa-page__table-wrap">
                <table class="mahasiswa-page__table mahasiswa-page__grade-table">
                    <thead>
                        <tr>
                            <th>Mata kuliah</th>
                            <th>Total (SKS)</th>
                            <th>Nilai</th>
                            <th>Pred.</th>
                            <th><span class="mahasiswa-page__sr-only">Rincian</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($courses as $course)
                            @php($finalGrade = $course->finalGrades->first())
                            <tr>
                                <td>
                                    <a class="mahasiswa-page__grade-course"
                                        href="{{ route('mahasiswa.courses.grades.show', $course->id) }}">
                                        <strong>{{ $course->name }}</strong>
                                        <span>{{ $course->code }}</span>
                                    </a>
                                </td>
                                <td>{{ $course->sks }}</td>
                                <td>{{ $finalGrade ? number_format((float) $finalGrade->total_score, 2) : '—' }}</td>
                                <td>
                                    @if ($finalGrade)
                                        <span class="mahasiswa-page__pill mahasiswa-page__pill--success">
                                            {{ $finalGrade->letter_grade }}
                                        </span>
                                    @else
                                        <span class="mahasiswa-page__pill">Belum tersedia</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('mahasiswa.courses.grades.show', $course->id) }}"
                                        class="mahasiswa-page__icon-link"
                                        aria-label="Lihat rincian nilai {{ $course->name }}">
                                        <span class="material-symbols-outlined">chevron_right</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="mahasiswa-page__empty">
                                    Belum ada mata kuliah {{ $status === 'active' ? 'aktif' : 'arsip' }} yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                        <tr class="mahasiswa-page__grade-summary">
                            <th colspan="3">IPS</th>
                            <td colspan="2">{{ $ips !== null ? number_format($ips, 2, ',', '.') : '—' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
