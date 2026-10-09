@extends('components.layout')

@section('content')
    <main class="mahasiswa-page mahasiswa-page--grades-index mahasiswa-grade-monitoring"
        x-data="{ selected: @js($status) }">
        <header class="mahasiswa-page__header">
            <div class="mahasiswa-page__header-copy">
                <div class="mahasiswa-grade-monitoring__eyebrow">
                    <span aria-hidden="true"></span>
                </div>
                <h1 class="mahasiswa-page__title">Monitoring Nilai</h1>
                <p class="mahasiswa-page__description">Pilih mata kuliah aktif atau arsip untuk melihat nilai dan predikat.</p>
            </div>
        </header>

        <section class="mahasiswa-grade-choices" aria-label="Pilih kategori mata kuliah">
            <button type="button"
                class="dosen-course-choice dosen-course-choice--active"
                :class="{ 'is-selected': selected === 'active' }"
                :aria-expanded="selected === 'active'"
                aria-controls="mahasiswa-grade-panel"
                @click="selected = selected === 'active' ? null : 'active'">
                <span class="dosen-course-choice__icon material-symbols-outlined" aria-hidden="true">menu_book</span>
                <span class="dosen-course-choice__content">
                    <span class="dosen-course-choice__eyebrow">SEMESTER BERJALAN</span>
                    <span class="dosen-course-choice__title">Mata Kuliah Aktif</span>
                    <span class="dosen-course-choice__description">Mata kuliah yang sedang kamu ikuti saat ini.</span>
                </span>
                <span class="dosen-course-choice__count">{{ $activeCourseCount }}</span>
                <span class="dosen-course-choice__toggle material-symbols-outlined" aria-hidden="true"
                    x-text="selected === 'active' ? 'expand_less' : 'expand_more'"></span>
            </button>

            <button type="button"
                class="dosen-course-choice dosen-course-choice--archived"
                :class="{ 'is-selected': selected === 'archived' }"
                :aria-expanded="selected === 'archived'"
                aria-controls="mahasiswa-grade-panel"
                @click="selected = selected === 'archived' ? null : 'archived'">
                <span class="dosen-course-choice__icon material-symbols-outlined" aria-hidden="true">inventory_2</span>
                <span class="dosen-course-choice__content">
                    <span class="dosen-course-choice__eyebrow">SEMESTER SEBELUMNYA</span>
                    <span class="dosen-course-choice__title">Mata Kuliah Arsip</span>
                    <span class="dosen-course-choice__description">Mata kuliah yang pernah kamu ikuti sebelumnya.</span>
                </span>
                <span class="dosen-course-choice__count">{{ $archivedCourseCount }}</span>
                <span class="dosen-course-choice__toggle material-symbols-outlined" aria-hidden="true"
                    x-text="selected === 'archived' ? 'expand_less' : 'expand_more'"></span>
            </button>
        </section>

        <section id="mahasiswa-grade-panel"
            class="dosen-course-panel mahasiswa-grade-panel"
            :class="{ 'dosen-course-panel--archived': selected === 'archived' }"
            x-cloak
            x-show="selected !== null"
            x-transition.opacity
            aria-live="polite">
            <div class="dosen-course-panel__header">
                <div>
                    <p class="dosen-course-panel__eyebrow"
                        x-text="selected === 'active' ? 'SEMESTER BERJALAN' : 'ARSIP MATA KULIAH'"></p>
                    <h2 x-text="selected === 'active' ? 'Mata Kuliah Aktif' : 'Mata Kuliah Arsip'"></h2>
                </div>
                <span class="dosen-course-panel__total">
                    <span x-text="selected === 'active' ? {{ $activeCourseCount }} : {{ $archivedCourseCount }}"></span>
                    mata kuliah
                </span>
            </div>

            <div class="dosen-course-list" x-show="selected === 'active'" id="mahasiswa-active-course-list">
                @forelse ($activeCourses as $course)
                    @php($finalGrade = $course->finalGrades->first())
                    <article class="dosen-course-row mahasiswa-grade-row">
                        <div class="dosen-course-row__identity">
                            <span class="dosen-course-row__code">{{ $course->code }}</span>
                            <h3>
                                <a href="{{ route('mahasiswa.grades.courses.show', $course->id) }}">
                                    {{ $course->name }}
                                </a>
                            </h3>
                            <span class="dosen-course-row__status dosen-course-row__status--active">
                                <span aria-hidden="true"></span>Aktif
                            </span>
                        </div>

                        <div class="dosen-course-row__metrics">
                            <div class="dosen-course-row__metric">
                                <span class="dosen-course-row__metric-icon material-symbols-outlined" aria-hidden="true">school</span>
                                <span class="dosen-course-row__metric-copy"><strong>{{ $course->sks }}</strong><small>SKS</small></span>
                            </div>
                            <div class="dosen-course-row__metric">
                                <span class="dosen-course-row__metric-icon material-symbols-outlined" aria-hidden="true">monitoring</span>
                                <span class="dosen-course-row__metric-copy">
                                    <strong>{{ $finalGrade ? number_format((float) $finalGrade->total_score, 2) : '—' }}</strong>
                                    <small>Nilai akhir</small>
                                </span>
                            </div>
                            <div class="dosen-course-row__metric">
                                <span class="dosen-course-row__metric-icon material-symbols-outlined" aria-hidden="true">workspace_premium</span>
                                <span class="dosen-course-row__metric-copy">
                                    <strong>{{ $finalGrade?->letter_grade ?? '—' }}</strong>
                                    <small>Predikat</small>
                                </span>
                            </div>
                        </div>

                        <a href="{{ route('mahasiswa.grades.courses.show', $course->id) }}"
                            class="mahasiswa-grade-row__link"
                            aria-label="Lihat rincian nilai {{ $course->name }}">
                            <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
                        </a>
                    </article>
                @empty
                    <div class="dosen-course-list__empty">
                        <span class="material-symbols-outlined" aria-hidden="true">menu_book</span>
                        <h3>Belum ada mata kuliah aktif</h3>
                        <p>Mata kuliah aktif yang kamu ikuti akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>

            <div class="dosen-course-list" x-show="selected === 'archived'" id="mahasiswa-archived-course-list">
                @forelse ($archivedCourses as $course)
                    @php($finalGrade = $course->finalGrades->first())
                    <article class="dosen-course-row dosen-course-row--archived mahasiswa-grade-row">
                        <div class="dosen-course-row__identity">
                            <span class="dosen-course-row__code">{{ $course->code }}</span>
                            <h3>
                                <a href="{{ route('mahasiswa.grades.courses.show', $course->id) }}">
                                    {{ $course->name }}
                                </a>
                            </h3>
                            <span class="dosen-course-row__status dosen-course-row__status--archived">
                                <span aria-hidden="true"></span>Arsip
                            </span>
                        </div>

                        <div class="dosen-course-row__metrics">
                            <div class="dosen-course-row__metric">
                                <span class="dosen-course-row__metric-icon material-symbols-outlined" aria-hidden="true">school</span>
                                <span class="dosen-course-row__metric-copy"><strong>{{ $course->sks }}</strong><small>SKS</small></span>
                            </div>
                            <div class="dosen-course-row__metric">
                                <span class="dosen-course-row__metric-icon material-symbols-outlined" aria-hidden="true">monitoring</span>
                                <span class="dosen-course-row__metric-copy">
                                    <strong>{{ $finalGrade ? number_format((float) $finalGrade->total_score, 2) : '—' }}</strong>
                                    <small>Nilai akhir</small>
                                </span>
                            </div>
                            <div class="dosen-course-row__metric">
                                <span class="dosen-course-row__metric-icon material-symbols-outlined" aria-hidden="true">workspace_premium</span>
                                <span class="dosen-course-row__metric-copy">
                                    <strong>{{ $finalGrade?->letter_grade ?? '—' }}</strong>
                                    <small>Predikat</small>
                                </span>
                            </div>
                        </div>

                        <a href="{{ route('mahasiswa.grades.courses.show', $course->id) }}"
                            class="mahasiswa-grade-row__link"
                            aria-label="Lihat rincian nilai {{ $course->name }}">
                            <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
                        </a>
                    </article>
                @empty
                    <div class="dosen-course-list__empty">
                        <span class="material-symbols-outlined" aria-hidden="true">inventory_2</span>
                        <h3>Belum ada mata kuliah arsip</h3>
                        <p>Mata kuliah yang telah diarsipkan akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </main>
@endsection
