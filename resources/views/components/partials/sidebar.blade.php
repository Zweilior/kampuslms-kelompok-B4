@php
    $navItems = [
        ['path' => 'dashboard',  'icon' => 'grid_view',      'label' => 'Dashboard',     'route' => 'dashboard'],
        ['path' => 'courses',    'icon' => 'menu_book',      'label' => 'Mata Kuliah',   'route' => 'courses.index'],
        ['path' => 'jadwal',     'icon' => 'calendar_today', 'label' => 'Jadwal Kuliah', 'route' => null],
        ['path' => 'tugas',      'icon' => 'assignment',     'label' => 'Tugas & Kuis',  'route' => null],
        ['path' => 'nilai',      'icon' => 'grade',          'label' => 'Nilai & KHS',   'route' => null],
        ['path' => 'pengumuman', 'icon' => 'campaign',       'label' => 'Pengumuman',    'route' => null],
    ];
    $activeNav = $activeNav ?? 'dashboard';
@endphp

<aside class="sidebar">

    {{-- Bagian atas: Logo + Navigasi --}}
    <div class="flex flex-col gap-space-xl">

        {{-- Logo --}}
        <div class="flex items-center gap-space-sm px-space-sm">
            <span class="material-symbols-outlined text-primary text-headline-lg">school</span>
            <div class="flex flex-col">
                <span class="font-headline-sm text-headline-sm tracking-tight leading-none">EduSpace</span>
                <span class="font-label-sm text-label-sm text-primary tracking-wider uppercase mt-space-2xs">
                    Learning Management
                </span>
            </div>
        </div>

        {{-- Navigasi --}}
        <nav class="sidebar-nav">
            @foreach ($navItems as $item)
                @php $isActive = $activeNav === $item['path']; @endphp
                <a href="{{ $item['route'] ? route($item['route']) : '#' }}"
                   class="flex items-center gap-space-sm px-space-md py-space-sm rounded-xl
                          {{ $isActive
                              ? 'bg-secondary-container text-on-secondary-container font-semibold shadow-[0_0_16px_rgba(175,209,136,0.15)]'
                              : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-headline-sm">{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>

    {{-- Bagian bawah: Status Sistem --}}
    <div class="px-space-xs">
        <div class="bg-surface-container p-space-md rounded-xl flex items-center justify-between shadow-[0_1px_8px_rgba(0,0,0,0.12)]">
            <div class="flex flex-col">
                <span class="font-label-sm text-label-sm text-on-surface-variant">Sistem Status</span>
                <span class="font-label-lg text-label-lg text-secondary flex items-center gap-space-2xs mt-space-2xs">
                    <span class="w-2 h-2 rounded-full bg-secondary inline-block animate-pulse-soft"></span>
                    Kampus Online
                </span>
            </div>
            <span class="material-symbols-outlined text-on-surface-variant text-headline-sm">dns</span>
        </div>
    </div>
</aside>