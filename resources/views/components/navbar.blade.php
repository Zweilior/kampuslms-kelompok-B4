@props(['activeNav' => 'dashboard'])

@php
    use Illuminate\Support\Facades\Auth;

    // Navbar hanya muncul saat login, jadi aman ambil dari Auth
    $user     = Auth::user();
    $userRole = $user->role ?? 'mahasiswa';
    $userName = $user->name ?? 'User';

    // Route dinamis per role
    $dashboardRoute = match ($userRole) {
        'admin'     => 'admin.dashboard',
        'dosen'     => 'dosen.dashboard',
        'mahasiswa' => 'mahasiswa.dashboard',
        default     => 'dashboard',
    };
    $coursesRoute = match ($userRole) {
        'admin'     => 'admin.courses.index',
        'dosen'     => 'dosen.courses.index',
        'mahasiswa' => 'mahasiswa.courses.index',
        default     => 'courses.index',
    };

    // Penanda menu aktif
    $activeNav = match (true) {
        request()->routeIs('admin.users.*')       => 'users',
        request()->routeIs('admin.courses.*')     => 'courses',
        request()->routeIs('admin.materials.*')   => 'materials',
        request()->routeIs('admin.assignments.*') => 'assignments',
        request()->routeIs('admin.grades.*')      => 'grades',

        request()->routeIs('dosen.dashboard')                              => 'dashboard',
        request()->routeIs('dosen.courses.materials.*')                    => 'materials',
        request()->routeIs('dosen.courses.assignments.*')                  => 'assignments',
        request()->routeIs('dosen.grades.*')                               => 'grades',

        request()->routeIs('mahasiswa.courses.*') => 'courses',

        request()->routeIs('dashboard', 'admin.dashboard', 'dosen.dashboard', 'mahasiswa.dashboard') => 'dashboard',
        default => $activeNav,
    };

    // Menu dasar (semua role)
    $navItems = [
        ['path' => 'dashboard', 'icon' => 'grid_view', 'label' => 'Dashboard',   'route' => $dashboardRoute],
        ['path' => 'courses',   'icon' => 'menu_book', 'label' => 'Mata Kuliah', 'route' => $coursesRoute],
    ];

    // ==================== MENU KHUSUS ADMIN ====================
    if ($userRole === 'admin') {
        $navItems[] = ['path' => 'users',       'icon' => 'group',          'label' => 'Manajemen User',   'route' => 'admin.users.index'];
        $navItems[] = ['path' => 'materials',   'icon' => 'calendar_today', 'label' => 'Materi',           'route' => 'admin.materials.index'];
        $navItems[] = ['path' => 'assignments', 'icon' => 'assignment',     'label' => 'Tugas',            'route' => 'admin.assignments.index'];
        $navItems[] = ['path' => 'grades',      'icon' => 'grade',          'label' => 'Monitoring Nilai', 'route' => 'admin.grades.index'];
    }

    // ==================== MENU KHUSUS DOSEN ====================
    if ($userRole === 'dosen') {
        $navItems = [
            ['path' => 'dashboard',   'icon' => 'grid_view',   'label' => 'Dashboard',        'route' => 'dosen.dashboard'],
            ['path' => 'materials',   'icon' => 'description', 'label' => 'Materi',           'route' => 'dosen.courses.index'],
            ['path' => 'assignments', 'icon' => 'assignment',  'label' => 'Tugas',            'route' => 'dosen.courses.index'],
            ['path' => 'grades',      'icon' => 'grade',       'label' => 'Monitoring Nilai', 'route' => 'dosen.grades.index'],
        ];
    }

    // ==================== MENU KHUSUS MAHASISWA ====================
    if ($userRole === 'mahasiswa') {
        $navItems = [
            ['path' => 'dashboard', 'icon' => 'grid_view', 'label' => 'Dashboard',   'route' => 'mahasiswa.dashboard'],
            ['path' => 'courses',   'icon' => 'menu_book', 'label' => 'Mata Kuliah', 'route' => 'mahasiswa.courses.index'],
        ];
    }
@endphp

<header class="navbar">
    <nav class="navbar__inner">

        {{-- Logo / Brand --}}
        <a href="{{ route($dashboardRoute) }}" class="navbar__brand">
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
                    <a href="{{ route($item['route'], $item['query'] ?? []) }}"
                        class="navbar__link {{ $isActive ? 'is-active' : '' }}">
                        <span class="material-symbols-outlined">{{ $item['icon'] }}</span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        {{-- Right side: Status + User Info + Logout --}}
        <div class="navbar__right">

            {{-- Status Online --}}
            <div class="navbar__status">
                <span class="navbar__status-dot"></span>
                <span class="navbar__status-text">Online</span>
            </div>

            {{-- User Info --}}
            <div style="display: flex; align-items: center; gap: 10px; margin-left: 10px;">
                <div class="navbar__avatar" title="{{ $userName }}">
                    {{ strtoupper(substr($userName, 0, 1)) }}
                </div>
                <div style="display: flex; flex-direction: column; line-height: 1.2;">
                    <span class="navbar__user-name" style="font-size: 0.85rem; font-weight: 600;">
                        {{ $userName }}
                    </span>
                    <span class="navbar__user-role" style="font-size: 0.7rem; text-transform: capitalize;">
                        {{ $userRole }}
                    </span>
                </div>
            </div>

            @auth
                <form action="{{ route('logout') }}" method="POST" style="display: inline; margin-left: 10px;">
                    @csrf
                    <button type="submit" class="navbar__link"
                        style="background: none; border: none; cursor: pointer; color: #ef4444; padding: 8px; display: flex; align-items: center;"
                        title="Logout">
                        <span class="material-symbols-outlined">logout</span>
                    </button>
                </form>
            @endauth

        </div>

    </nav>
</header>