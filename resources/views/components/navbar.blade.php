@props(['activeNav' => 'dashboard'])

@php
    $navItems = [
        ['path' => 'dashboard',  'icon' => 'grid_view',      'label' => 'Dashboard',     'route' => 'dashboard'],
        ['path' => 'courses',    'icon' => 'menu_book',      'label' => 'Mata Kuliah',   'route' => 'courses.index'],
        ['path' => 'jadwal',     'icon' => 'calendar_today', 'label' => 'Jadwal Kuliah', 'route' => null],
        ['path' => 'tugas',      'icon' => 'assignment',     'label' => 'Tugas & Kuis',  'route' => null],
        ['path' => 'nilai',      'icon' => 'grade',          'label' => 'Nilai & KHS',   'route' => null],
        ['path' => 'pengumuman', 'icon' => 'campaign',       'label' => 'Pengumuman',    'route' => null],
    ];
@endphp

<header class="navbar">
    <nav class="navbar__inner">

        {{-- Logo / Brand --}}
        <a href="{{ route('dashboard') }}" class="navbar__brand">
            <span class="material-symbols-outlined navbar__brand-icon">school</span>
            <div class="navbar__brand-text">
                <span class="navbar__brand-name">EduSpace</span>
                <span class="navbar__brand-tag">Learning Management</span>
            </div>
        </a>

        {{-- Nav Links --}}
        <ul class="navbar__links">
            @foreach ($navItems as $item)
                @php $isActive = $activeNav === $item['path']; @endphp
                <li>
                    <a href="{{ $item['route'] ? route($item['route']) : '#' }}"
                       class="navbar__link {{ $isActive ? 'is-active' : '' }}">
                        <span class="material-symbols-outlined">{{ $item['icon'] }}</span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        {{-- Right side: Status + Avatar --}}
        <div class="navbar__right">

            <div class="navbar__status">
                <span class="navbar__status-dot"></span>
                <span class="navbar__status-text">Online</span>
            </div>

            <div class="navbar__avatar" title="Sekolah">
                M
            </div>

        </div>

    </nav>
</header>