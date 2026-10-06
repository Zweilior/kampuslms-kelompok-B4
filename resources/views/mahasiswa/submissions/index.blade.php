@extends('components.layout')

@section('content')
    <div class="mahasiswa-page mahasiswa-page--submissions">
        <header class="mahasiswa-page__header">
            <div class="mahasiswa-page__header-copy">
                <a href="{{ route('mahasiswa.courses.assignments.index', $course->id) }}" class="mahasiswa-page__back">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Kembali ke daftar tugas
                </a>
                <h1 class="mahasiswa-page__title">Status pengumpulan & nilai</h1>
                <p class="mahasiswa-page__description">{{ $assignment->title }} · {{ $course->name }}</p>
            </div>
            <span class="mahasiswa-page__icon-box"><span class="material-symbols-outlined">grade</span></span>
        </header>

        <section class="mahasiswa-page__card mahasiswa-page__table-wrap">
            <table class="mahasiswa-page__table">
                <thead>
                    <tr>
                        <th>Nama File</th>
                        <th>Waktu Dikumpul</th>
                        <th>Catatan</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $submission)
                        <tr>
                            <td class="fw-bold">{{ $submission->original_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y, H:i') }}</td>
                            <td>{{ $submission->note ?? '-' }}</td>
                            <td>
                                @if ($submission->score !== null)
                                    <span
                                        class="mahasiswa-page__pill mahasiswa-page__pill--success">{{ $submission->score }}
                                        / {{ $assignment->max_score }}</span>
                                @else
                                    <span class="mahasiswa-page__pill">Belum dinilai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="mahasiswa-page__empty">Kamu belum mengumpulkan tugas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </div>
@endsection
