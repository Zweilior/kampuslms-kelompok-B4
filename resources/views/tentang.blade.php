@php
    $developers = [
        [
            'name' => 'Muhammad Arif Saputra',
            'nim' => '10241044',
            'program_studi' => 'Sistem Informasi',
            'role' => 'Full-stack Developer',
            'description' => 'Memimpin koordinasi tim pengembang sekaligus berkontribusi membangun layanan termasuk dengan migrasi dan logika sistem back-end EduSpace.',
            'email' => '10241044@student.itk.ac.id',
            'github' => 'https://github.com/Zweilior',
            'initials' => 'MA',
            'first_name' => 'Muhammad Arif',
            'last_name' => 'Saputra',
            'photo' => 'image/arif.png',
            'tone' => 'moss',
        ],
        [
            'name' => 'Marchelino Senduk Kaunang',
            'nim' => '10241040',
            'program_studi' => 'Sistem Informasi',
            'role' => 'Back-end Developer',
            'description' => 'Mengembangkan pengelolaan data migrasi yang mendukung fitur EduSpace.',
            'email' => '10241040@student.itk.ac.id',
            'github' => 'https://github.com/Marchelinosk',
            'initials' => 'MS',
            'first_name' => 'Marchelino Senduk',
            'last_name' => 'Kaunang',
            'photo' => 'image/marchell.png',
            'tone' => 'sage',
        ],
        [
            'name' => 'Moh. Irsyad Fiqi Ferdiansyah Difa Nanda',
            'nim' => '10241042',
            'program_studi' => 'Sistem Informasi',
            'role' => 'Front-end Developer',
            'description' => 'Membangun antarmuka EduSpace yang responsif dan membantu pengguna mengakses fitur akademik dengan mudah.',
            'email' => '10241042@student.itk.ac.id',
            'github' => 'http://github.com/ferdiansyaafq',
            'initials' => 'MI',
            'first_name' => 'Moh. Irsyad Fiqi',
            'last_name' => 'Ferdiansyah Difa Nanda',
            'photo' => 'image/irsyad.png',
            'tone' => 'gold',
        ],
        [
            'name' => 'Laudya Aprilia Khoirum',
            'nim' => '10241038',
            'program_studi' => 'Sistem Informasi',
            'role' => 'Front-end Developer',
            'description' => 'Merancang dan menyempurnakan tampilan halaman agar pengalaman belajar di EduSpace tetap jelas dan nyaman.',
            'email' => '10241038@student.itk.ac.id',
            'github' => 'https://github.com/xcyltra',
            'initials' => 'LA',
            'first_name' => 'Laudya Aprilia',
            'last_name' => 'Khoirum',
            'photo' => 'image/laudya.png',
            'tone' => 'clay',
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <title>Tentang Kami - EduSpace</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @vite(['resources/css/app.css', 'resources/css/about.css'])
</head>
<body class="about-page">
    <header class="about-header">
        <a class="about-brand" href="{{ route('login') }}" aria-label="EduSpace, kembali ke login">
            <span class="material-symbols-outlined">school</span>
            <span>EduSpace</span>
        </a>
        <a class="about-back-link" href="{{ route('login') }}">
            <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
            Kembali ke Login
        </a>
    </header>

    <main class="about-main" x-data="{
        openMember: null,
        lastTrigger: null,
        toggleMember(index, button) {
            if (this.openMember === index) {
                this.closeMember();
                return;
            }

            this.openMember = index;
            this.lastTrigger = button;
            document.documentElement.classList.add('about-modal-open');
            this.$nextTick(() => {
                document.querySelector(`#developer-info-${index} .developer-info__close`)?.focus();
            });
        },
        closeMember() {
            this.openMember = null;
            document.documentElement.classList.remove('about-modal-open');
            this.$nextTick(() => this.lastTrigger?.focus());
        }
    }" @keydown.escape.window="closeMember()">
        <section class="about-intro">
            <p class="about-eyebrow">TENTANG EDUSPACE</p>
            <h1>Belajar dan bertumbuh <span>dalam satu ruang.</span></h1>
            <p class="about-description">
                EduSpace adalah Learning Management System yang membantu mahasiswa dan dosen
                menjalankan kegiatan akademik secara lebih teratur. Materi kuliah, tugas,
                pengumpulan, dan informasi nilai tersedia dalam satu platform yang mudah diakses.
            </p>
        </section>

        <section class="team-section" aria-labelledby="team-heading">
            <div class="section-heading">
                <div>
                    <p class="about-eyebrow">DI BALIK EDUSPACE</p>
                    <h2 id="team-heading">Tim Pengembang</h2>
                </div>
                <p>Empat orang, satu ruang belajar yang terus berkembang.</p>
            </div>

            <div class="team-grid">
                @foreach ($developers as $index => $developer)
                    <article class="developer-item">
                        <div class="developer-card developer-card--{{ $developer['tone'] }}">
                            <div class="developer-card__art" aria-hidden="true">
                                @if (!empty($developer['photo']))
                                    <img src="{{ asset($developer['photo']) }}" alt="" loading="lazy">
                                @else
                                    <span class="developer-card__initials">{{ $developer['initials'] }}</span>
                                @endif
                            </div>
                            <div class="developer-card__shade"></div>
                            <div class="developer-card__footer">
                                <div class="developer-card__heading">
                                    <p class="developer-card__role">{{ $developer['role'] }}</p>
                                    <h3 class="developer-card__name">
                                        <span>{{ $developer['first_name'] }}</span>
                                        <span>{{ $developer['last_name'] }}</span>
                                    </h3>
                                </div>
                                <button
                                    class="developer-card__open"
                                    type="button"
                                    @click="toggleMember({{ $index }}, $el)"
                                    :aria-expanded="openMember === {{ $index }}"
                                    aria-controls="developer-info-{{ $index }}"
                                    aria-label="Lihat profil {{ $developer['name'] }}">
                                    <span class="material-symbols-outlined" aria-hidden="true">expand_content</span>
                                </button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @foreach ($developers as $index => $developer)
                <section
                    class="developer-info"
                    id="developer-info-{{ $index }}"
                    x-cloak
                    x-show="openMember === {{ $index }}"
                    x-transition:enter="developer-info-enter"
                    x-transition:enter-start="developer-info-enter-start"
                    x-transition:enter-end="developer-info-enter-end"
                    x-transition:leave="developer-info-leave"
                    x-transition:leave-start="developer-info-leave-start"
                    x-transition:leave-end="developer-info-leave-end"
                    @click.self="closeMember()"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="developer-info-heading-{{ $index }}">
                    <div class="developer-info__panel">
                        <div class="developer-info__topline">
                            <span class="developer-info__label">PROFIL PENGEMBANG</span>
                            <span class="developer-info__number">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <button class="developer-info__close" type="button" @click="closeMember()" aria-label="Tutup informasi pengembang">
                                <span class="material-symbols-outlined" aria-hidden="true">close</span>
                            </button>
                        </div>
                        <div class="developer-info__hero developer-card--{{ $developer['tone'] }}">
                            <div class="developer-info__portrait">
                                @if (!empty($developer['photo']))
                                    <img src="{{ asset($developer['photo']) }}" alt="Foto {{ $developer['name'] }}">
                                @else
                                    <span>{{ $developer['initials'] }}</span>
                                @endif
                            </div>
                            <div class="developer-info__identity">
                                <span class="developer-info__hero-label">PENGEMBANG EDUSPACE</span>
                                <h3 class="developer-info__name">
                                    <span>{{ $developer['first_name'] }}</span>
                                    <span>{{ $developer['last_name'] }}</span>
                                </h3>
                                <p class="developer-info__role">{{ $developer['role'] }}</p>
                            </div>
                        </div>
                        <div class="developer-info__content">
                            <dl class="developer-info__details">
                                <div>
                                    <dt>NIM</dt>
                                    <dd>{{ $developer['nim'] }}</dd>
                                </div>
                                <div>
                                    <dt>Program Studi</dt>
                                    <dd>{{ $developer['program_studi'] }}</dd>
                                </div>
                            </dl>
                            <p class="developer-info__description">{{ $developer['description'] }}</p>
                            <div class="developer-info__contacts">
                                @if ($developer['github'])
                                    <a class="developer-info__contact" href="{{ $developer['github'] }}"
                                        target="_blank" rel="noopener noreferrer" aria-label="GitHub {{ $developer['name'] }}">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path fill="currentColor" d="M12 .9a11.1 11.1 0 0 0-3.51 21.63c.55.1.76-.24.76-.53v-2.07c-3.1.68-3.76-1.32-3.76-1.32-.5-1.3-1.23-1.65-1.23-1.65-1.01-.69.08-.68.08-.68 1.12.08 1.71 1.15 1.71 1.15 1 .1.75 2.32 3.85 1.65.1-.72.4-1.21.7-1.49-2.48-.28-5.08-1.24-5.08-5.52 0-1.22.44-2.22 1.15-3-.12-.28-.5-1.42.11-2.96 0 0 .94-.3 3.05 1.15a10.6 10.6 0 0 1 5.55 0c2.12-1.44 3.05-1.15 3.05-1.15.61 1.54.23 2.68.12 2.96.71.78 1.14 1.78 1.14 3 0 4.29-2.6 5.24-5.09 5.51.4.35.75 1.02.75 2.06V22c0 .29.2.63.76.52A11.1 11.1 0 0 0 12 .9Z"/>
                                        </svg>
                                        <span>GitHub</span>
                                    </a>
                                @else
                                    <span class="developer-info__contact developer-info__contact--unavailable" aria-disabled="true">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path fill="currentColor" d="M12 .9a11.1 11.1 0 0 0-3.51 21.63c.55.1.76-.24.76-.53v-2.07c-3.1.68-3.76-1.32-3.76-1.32-.5-1.3-1.23-1.65-1.23-1.65-1.01-.69.08-.68.08-.68 1.12.08 1.71 1.15 1.71 1.15 1 .1.75 2.32 3.85 1.65.1-.72.4-1.21.7-1.49-2.48-.28-5.08-1.24-5.08-5.52 0-1.22.44-2.22 1.15-3-.12-.28-.5-1.42.11-2.96 0 0 .94-.3 3.05 1.15a10.6 10.6 0 0 1 5.55 0c2.12-1.44 3.05-1.15 3.05-1.15.61 1.54.23 2.68.12 2.96.71.78 1.14 1.78 1.14 3 0 4.29-2.6 5.24-5.09 5.51.4.35.75 1.02.75 2.06V22c0 .29.2.63.76.52A11.1 11.1 0 0 0 12 .9Z"/>
                                        </svg>
                                        <span>GitHub</span>
                                    </span>
                                @endif
                                <a class="developer-info__contact" href="mailto:{{ $developer['email'] }}"
                                    aria-label="Email {{ $developer['name'] }}">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill="currentColor" d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5L4 8V6l8 5 8-5v2Z"/>
                                    </svg>
                                    <span>{{ $developer['email'] }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            @endforeach
        </section>

        <section class="system-section" aria-labelledby="system-heading">
            <div class="system-card">
                <div class="system-card__heading">
                    <span class="material-symbols-outlined" aria-hidden="true">info</span>
                    <div>
                        <p class="about-eyebrow">PLATFORM</p>
                        <h2 id="system-heading">Informasi Sistem</h2>
                    </div>
                </div>
                <dl class="system-details">
                    <div>
                        <dt>Aplikasi</dt>
                        <dd>EduSpace Learning Management System</dd>
                    </div>
                    <div>
                        <dt>Versi</dt>
                        <dd>1.0.0</dd>
                    </div>
                    <div>
                        <dt>Framework</dt>
                        <dd>Laravel 12</dd>
                    </div>
                    <div>
                        <dt>Status</dt>
                        <dd class="system-status"><span></span>Online</dd>
                    </div>
                </dl>
            </div>
        </section>
    </main>

    <footer class="about-footer">
        <span>© 2026 EduSpace</span>
        <span>Institut Teknologi Kalimantan</span>
    </footer>
</body>
</html>
