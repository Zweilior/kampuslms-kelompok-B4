@extends('components.layout')

@section('content')
    <div class="mahasiswa-rubric">
        <header class="mahasiswa-rubric__header">
            <a href="{{ route('mahasiswa.courses.show', $course->id) }}" class="mahasiswa-rubric__back">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                Kembali ke kelas
            </a>
            <div class="mahasiswa-rubric__heading">
                <div>
                    <div class="mahasiswa-rubric__eyebrow">
                        <span>AKADEMIK</span>
                        <span aria-hidden="true"></span>
                        <span>RUBRIK PENILAIAN</span>
                    </div>
                    <h1>{{ $course->name }}</h1>
                    <p>Lihat komponen penilaian, bobot, dan tugas yang termasuk dalam rubrik mata kuliah ini.</p>
                </div>
                <span class="mahasiswa-rubric__course-code">{{ $course->code }}</span>
            </div>
        </header>

        <section class="mahasiswa-rubric__summary" aria-label="Ringkasan rubrik">
            <article class="mahasiswa-rubric__summary-card mahasiswa-rubric__summary-card--weight">
                <span class="material-symbols-outlined" aria-hidden="true">pie_chart</span>
                <span class="mahasiswa-rubric__summary-copy">
                    <span class="mahasiswa-rubric__summary-label">TOTAL BOBOT RUBRIK</span>
                    <strong>{{ number_format((float) $totalWeight, 2) }}<small>%</small></strong>
                </span>
                <span class="mahasiswa-rubric__summary-note">Akumulasi bobot penilaian</span>
            </article>
            <article class="mahasiswa-rubric__summary-card mahasiswa-rubric__summary-card--components">
                <span class="material-symbols-outlined" aria-hidden="true">format_list_bulleted</span>
                <span class="mahasiswa-rubric__summary-copy">
                    <span class="mahasiswa-rubric__summary-label">KOMPONEN PENILAIAN</span>
                    <strong>{{ $gradeComponents->count() }}</strong>
                </span>
                <span class="mahasiswa-rubric__summary-note">Komponen yang ditetapkan dosen</span>
            </article>
            <article class="mahasiswa-rubric__summary-card mahasiswa-rubric__summary-card--assignments">
                <span class="material-symbols-outlined" aria-hidden="true">assignment</span>
                <span class="mahasiswa-rubric__summary-copy">
                    <span class="mahasiswa-rubric__summary-label">TUGAS TERKAIT</span>
                    <strong>{{ $assignmentCount }}</strong>
                </span>
                <span class="mahasiswa-rubric__summary-note">Tugas yang dipublikasikan</span>
            </article>
        </section>

        <section class="mahasiswa-rubric__panel">
            <div class="mahasiswa-rubric__panel-header">
                <div>
                    <span class="mahasiswa-rubric__panel-eyebrow">RINCIAN KOMPONEN</span>
                    <h2>Rubrik penilaian</h2>
                </div>
                <span class="mahasiswa-rubric__panel-count">{{ $gradeComponents->count() }} komponen</span>
            </div>

            @forelse ($gradeComponents as $component)
                <article class="mahasiswa-rubric__component">
                    <div class="mahasiswa-rubric__component-heading">
                        <span class="mahasiswa-rubric__component-icon material-symbols-outlined" aria-hidden="true">checklist</span>
                        <div class="mahasiswa-rubric__component-copy">
                            <h3>{{ $component->name }}</h3>
                            <span>{{ $component->assignments->count() }} tugas dipublikasikan</span>
                        </div>
                        <span class="mahasiswa-rubric__weight">
                            {{ number_format((float) $component->weight, 2) }}<small>% bobot</small>
                        </span>
                    </div>

                    @if ($component->assignments->isNotEmpty())
                        <div class="mahasiswa-rubric__assignments">
                            @foreach ($component->assignments as $assignment)
                                @php($submission = $assignment->submissions->first())
                                <a href="{{ $submission
                                    ? route('mahasiswa.courses.assignments.submissions.index', [$course->id, $assignment->id])
                                    : route('mahasiswa.courses.assignments.submissions.create', [$course->id, $assignment->id]) }}"
                                    class="mahasiswa-rubric__assignment">
                                    <span class="mahasiswa-rubric__assignment-icon material-symbols-outlined" aria-hidden="true">description</span>
                                    <span class="mahasiswa-rubric__assignment-copy">
                                        <strong>{{ $assignment->title }}</strong>
                                        <span>Tenggat {{ $assignment->due_at->format('d M Y, H:i') }}</span>
                                    </span>
                                    @if ($submission?->grade)
                                        <span class="mahasiswa-rubric__status mahasiswa-rubric__status--graded">
                                            Nilai {{ number_format((float) $submission->grade->score, 2) }} / {{ $assignment->max_score }}
                                        </span>
                                    @elseif ($submission)
                                        <span class="mahasiswa-rubric__status">Menunggu penilaian</span>
                                    @else
                                        <span class="mahasiswa-rubric__status mahasiswa-rubric__status--pending">Belum dikumpulkan</span>
                                    @endif
                                    <span class="material-symbols-outlined mahasiswa-rubric__arrow" aria-hidden="true">chevron_right</span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="mahasiswa-rubric__no-assignments">Belum ada tugas yang dipublikasikan untuk komponen ini.</p>
                    @endif
                </article>
            @empty
                <div class="mahasiswa-rubric__empty">
                    <span class="material-symbols-outlined" aria-hidden="true">rule</span>
                    <h3>Rubrik belum tersedia</h3>
                    <p>Dosen belum menambahkan komponen penilaian untuk mata kuliah ini.</p>
                </div>
            @endforelse
        </section>
    </div>
@endsection
