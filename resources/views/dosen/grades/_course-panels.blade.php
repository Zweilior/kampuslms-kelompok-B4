<main class="dosen-courses" x-data="{ selected: 'active' }">
        <header class="dosen-courses__header">
            <div class="dosen-courses__eyebrow">
                <span>AKADEMIK</span>
                <span aria-hidden="true"></span>
                <span>MONITORING NILAI</span>
            </div>
            <h1>Rekap Nilai Mata Kuliah</h1>
            <p>Pilih mata kuliah aktif atau arsip untuk melihat rata-rata nilai kelas dan membuka pengelolaan kelas.</p>
        </header>

        <section class="dosen-course-choices" aria-label="Pilih kategori mata kuliah">
            <button type="button"
                    class="dosen-course-choice dosen-course-choice--active"
                    :class="{ 'is-selected': selected === 'active' }"
                    :aria-expanded="selected === 'active'"
                    aria-controls="dosen-course-panel"
                    @click="selected = selected === 'active' ? null : 'active'">
                <span class="dosen-course-choice__icon material-symbols-outlined" aria-hidden="true">menu_book</span>
                <span class="dosen-course-choice__content">
                    <span class="dosen-course-choice__eyebrow">SEMESTER BERJALAN</span>
                    <span class="dosen-course-choice__title">Mata Kuliah Aktif</span>
                    <span class="dosen-course-choice__description">Kelas yang sedang Anda ampu saat ini.</span>
                </span>
                <span class="dosen-course-choice__count">{{ $activeCourses->count() }}</span>
                <span class="dosen-course-choice__toggle material-symbols-outlined" aria-hidden="true" x-text="selected === 'active' ? 'expand_less' : 'expand_more'"></span>
            </button>

            <button type="button"
                    class="dosen-course-choice dosen-course-choice--archived"
                    :class="{ 'is-selected': selected === 'archived' }"
                    :aria-expanded="selected === 'archived'"
                    aria-controls="dosen-course-panel"
                    @click="selected = selected === 'archived' ? null : 'archived'">
                <span class="dosen-course-choice__icon material-symbols-outlined" aria-hidden="true">inventory_2</span>
                <span class="dosen-course-choice__content">
                    <span class="dosen-course-choice__eyebrow">SEMESTER SEBELUMNYA</span>
                    <span class="dosen-course-choice__title">Mata Kuliah Arsip</span>
                    <span class="dosen-course-choice__description">Kelas yang pernah Anda ampu, tetapi tidak aktif semester ini.</span>
                </span>
                <span class="dosen-course-choice__count">{{ $archivedCourses->count() }}</span>
                <span class="dosen-course-choice__toggle material-symbols-outlined" aria-hidden="true" x-text="selected === 'archived' ? 'expand_less' : 'expand_more'"></span>
            </button>
        </section>

        <section id="dosen-course-panel"
                 class="dosen-course-panel"
                 :class="{ 'dosen-course-panel--archived': selected === 'archived' }"
                 x-cloak
                 x-show="selected !== null"
                 x-transition.opacity
                 aria-live="polite">
            <div class="dosen-course-panel__header">
                <div>
                    <p class="dosen-course-panel__eyebrow" x-text="selected === 'active' ? 'SEMESTER BERJALAN' : 'ARSIP PENGAMPUAN'"></p>
                    <h2 x-text="selected === 'active' ? 'Mata Kuliah Aktif' : 'Mata Kuliah Arsip'"></h2>
                </div>
                <span class="dosen-course-panel__total">
                    <span x-text="selected === 'active' ? {{ $activeCourses->count() }} : {{ $archivedCourses->count() }}"></span>
                    mata kuliah
                </span>
            </div>

            <div class="dosen-course-list" x-show="selected === 'active'" id="dosen-active-course-list">
                @forelse ($activeCourses as $course)
                    <article class="dosen-course-row">
                        <div class="dosen-course-row__identity">
                            <span class="dosen-course-row__code">{{ $course->code }}</span>
                            <h3>{{ $course->name }}</h3>
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
                                <span class="dosen-course-row__metric-icon material-symbols-outlined" aria-hidden="true">groups</span>
                                <span class="dosen-course-row__metric-copy"><strong>{{ $course->students_count }}</strong><small>Mahasiswa</small></span>
                            </div>
                            <div class="dosen-course-row__metric">
                                <span class="dosen-course-row__metric-icon material-symbols-outlined" aria-hidden="true">monitoring</span>
                                <span class="dosen-course-row__metric-copy">
                                    <strong>{{ $course->final_grades_avg_total_score !== null ? number_format((float) $course->final_grades_avg_total_score, 2) : '—' }}</strong>
                                    <small>Rata-rata nilai</small>
                                </span>
                            </div>
                        </div>

                        <div class="dosen-course-row__menu" x-data="{ menuOpen: false }" @click.outside="menuOpen = false" @keydown.escape.window="menuOpen = false">
                            <button type="button"
                                    class="dosen-course-row__menu-trigger"
                                    aria-label="Opsi {{ $course->name }}"
                                    aria-haspopup="true"
                                    :aria-expanded="menuOpen"
                                    @click.stop="menuOpen = !menuOpen">
                                <span class="material-symbols-outlined" aria-hidden="true">more_vert</span>
                            </button>
                            <div class="dosen-course-row__menu-popover"
                                 x-cloak
                                 x-show="menuOpen"
                                 x-transition.origin.top.right
                                 @click.stop>
                                <a href="{{ route('dosen.courses.materials.index', ['course' => $course, 'focus' => 'materials']) }}">
                                    <span class="material-symbols-outlined" aria-hidden="true">description</span>Lihat Materi
                                </a>
                                <a href="{{ route('dosen.courses.assignments.index', ['course' => $course, 'focus' => 'assignments']) }}">
                                    <span class="material-symbols-outlined" aria-hidden="true">assignment</span>Tugas dan Nilai
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="dosen-course-list__empty">
                        <span class="material-symbols-outlined" aria-hidden="true">menu_book</span>
                        <h3>Belum ada mata kuliah aktif</h3>
                        <p>Mata kuliah berstatus aktif akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>

            <div class="dosen-course-list" x-show="selected === 'archived'" id="dosen-archived-course-list">
                @forelse ($archivedCourses as $course)
                    <article class="dosen-course-row dosen-course-row--archived">
                        <div class="dosen-course-row__identity">
                            <span class="dosen-course-row__code">{{ $course->code }}</span>
                            <h3>{{ $course->name }}</h3>
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
                                <span class="dosen-course-row__metric-icon material-symbols-outlined" aria-hidden="true">groups</span>
                                <span class="dosen-course-row__metric-copy"><strong>{{ $course->students_count }}</strong><small>Mahasiswa</small></span>
                            </div>
                            <div class="dosen-course-row__metric">
                                <span class="dosen-course-row__metric-icon material-symbols-outlined" aria-hidden="true">monitoring</span>
                                <span class="dosen-course-row__metric-copy">
                                    <strong>{{ $course->final_grades_avg_total_score !== null ? number_format((float) $course->final_grades_avg_total_score, 2) : '—' }}</strong>
                                    <small>Rata-rata nilai</small>
                                </span>
                            </div>
                        </div>

                        <div class="dosen-course-row__menu" x-data="{ menuOpen: false }" @click.outside="menuOpen = false" @keydown.escape.window="menuOpen = false">
                            <button type="button"
                                    class="dosen-course-row__menu-trigger"
                                    aria-label="Opsi {{ $course->name }}"
                                    aria-haspopup="true"
                                    :aria-expanded="menuOpen"
                                    @click.stop="menuOpen = !menuOpen">
                                <span class="material-symbols-outlined" aria-hidden="true">more_vert</span>
                            </button>
                            <div class="dosen-course-row__menu-popover"
                                 x-cloak
                                 x-show="menuOpen"
                                 x-transition.origin.top.right
                                 @click.stop>
                                <a href="{{ route('dosen.courses.materials.index', ['course' => $course, 'focus' => 'materials']) }}">
                                    <span class="material-symbols-outlined" aria-hidden="true">description</span>Lihat Materi
                                </a>
                                <a href="{{ route('dosen.courses.assignments.index', ['course' => $course, 'focus' => 'assignments']) }}">
                                    <span class="material-symbols-outlined" aria-hidden="true">assignment</span>Tugas dan Nilai
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="dosen-course-list__empty">
                        <span class="material-symbols-outlined" aria-hidden="true">inventory_2</span>
                        <h3>Belum ada mata kuliah arsip</h3>
                        <p>Mata kuliah yang sudah tidak aktif akan tersimpan di sini.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </main>
