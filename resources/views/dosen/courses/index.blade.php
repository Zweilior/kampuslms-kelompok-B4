<x-layout title="Mata Kuliah Diampu - Dosen" active-nav="courses">
    <main class="dosen-courses max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ selected: 'active' }">
        @php
            $focus = request('focus') === 'assignments' ? 'assignments' : 'materials';
        @endphp
        <header class="mb-space-lg lms-page-header">
            <div class="lms-page-header__copy">
                <div class="flex items-center gap-space-xs mb-space-2xs">
                    <span class="px-space-xs py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm uppercase">Dosen</span>
                    <span class="w-1 h-1 rounded-full bg-outline"></span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Daftar pengampu</span>
                </div>
                <h1 class="font-headline-lg text-headline-lg text-on-surface">Mata Kuliah yang Anda Ampu</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">Pilih mata kuliah untuk melihat detail dan mengelola kelas.</p>
            </div>
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
                    <span class="dosen-course-choice__description">Mata kuliah yang sedang Anda ampu saat ini.</span>
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
                    <span class="dosen-course-choice__description">Mata kuliah yang pernah Anda ampu.</span>
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
                            <span class="dosen-course-row__status dosen-course-row__status--active"><span aria-hidden="true"></span>Aktif</span>
                        </div>
                        <div class="dosen-course-row__metrics">
                            <div class="dosen-course-row__metric">
                                <span class="dosen-course-row__metric-icon material-symbols-outlined" aria-hidden="true">school</span>
                                <span class="dosen-course-row__metric-copy"><strong>{{ $course->sks }}</strong><small>SKS</small></span>
                            </div>
                            <div class="dosen-course-row__metric">
                                <span class="dosen-course-row__metric-icon material-symbols-outlined" aria-hidden="true">person</span>
                                <span class="dosen-course-row__metric-copy"><strong>{{ $course->lecturer->name }}</strong><small>Dosen pengampu</small></span>
                            </div>
                        </div>
                        <a href="{{ route('dosen.courses.show', ['course' => $course, 'focus' => $focus]) }}" class="dosen-course-row__link">Lihat mata kuliah</a>
                    </article>
                @empty
                    <div class="dosen-course-list__empty">
                        <span class="material-symbols-outlined" aria-hidden="true">menu_book</span>
                        <h3>Belum ada mata kuliah aktif</h3>
                        <p>Mata kuliah aktif akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>

            <div class="dosen-course-list" x-show="selected === 'archived'" id="dosen-archived-course-list">
                @forelse ($archivedCourses as $course)
                    <article class="dosen-course-row dosen-course-row--archived">
                        <div class="dosen-course-row__identity">
                            <span class="dosen-course-row__code">{{ $course->code }}</span>
                            <h3>{{ $course->name }}</h3>
                            <span class="dosen-course-row__status dosen-course-row__status--archived"><span aria-hidden="true"></span>Arsip</span>
                        </div>
                        <div class="dosen-course-row__metrics">
                            <div class="dosen-course-row__metric">
                                <span class="dosen-course-row__metric-icon material-symbols-outlined" aria-hidden="true">school</span>
                                <span class="dosen-course-row__metric-copy"><strong>{{ $course->sks }}</strong><small>SKS</small></span>
                            </div>
                            <div class="dosen-course-row__metric">
                                <span class="dosen-course-row__metric-icon material-symbols-outlined" aria-hidden="true">person</span>
                                <span class="dosen-course-row__metric-copy"><strong>{{ $course->lecturer->name }}</strong><small>Dosen pengampu</small></span>
                            </div>
                        </div>
                        <a href="{{ route('dosen.courses.show', ['course' => $course, 'focus' => $focus]) }}" class="dosen-course-row__link">Lihat mata kuliah</a>
                    </article>
                @empty
                    <div class="dosen-course-list__empty">
                        <span class="material-symbols-outlined" aria-hidden="true">inventory_2</span>
                        <h3>Belum ada mata kuliah arsip</h3>
                        <p>Mata kuliah arsip akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </main>
</x-layout>
