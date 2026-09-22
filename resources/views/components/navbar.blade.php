@props(['activeNav' => 'dashboard'])

@php
    use Illuminate\Support\Facades\Auth;

    if (Auth::check()) {
        $userRole = Auth::user()->role;
        $userName = Auth::user()->name;
    } else {
        $userRole = session('simulated_role', 'mahasiswa'); // Default ke mahasiswa jika belum pilih
        $userName = ucfirst($userRole) ;
    }

    // Menu Dasar (Semua Role Bisa Akses)
    $navItems = [
        ['path' => 'dashboard',  'icon' => 'grid_view',      'label' => 'Dashboard',        'route' => 'dashboard'],
        ['path' => 'courses',    'icon' => 'menu_book',      'label' => 'Mata Kuliah',      'route' => 'courses.index'],
    ];

    // Menu Khusus Admin
    if ($userRole === 'admin') {
        $navItems[] = ['path' => 'users', 'icon' => 'campaign', 'label' => 'Manajemen User', 'route' => 'users.index'];
    }

    // Menu Lainnya (Preview - Route masih null)
    $navItems[] = ['path' => 'jadwal', 'icon' => 'calendar_today', 'label' => 'Materi',           'route' => null];
    $navItems[] = ['path' => 'tugas',  'icon' => 'assignment',     'label' => 'Tugas',            'route' => null];
    $navItems[] = ['path' => 'nilai',  'icon' => 'grade',          'label' => 'Monitoring Nilai', 'route' => null];
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

        {{-- Right side: Status + User Info + Logout --}}
        <div class="navbar__right">
            
            {{-- Status Online --}}
            <div class="navbar__status">
                <span class="navbar__status-dot"></span>
                <span class="navbar__status-text">Online</span>
            </div>

            {{-- User Info (Nama & Role) --}}
            <div style="display: flex; align-items: center; gap: 10px; margin-left: 10px;">
                {{-- Avatar dengan Inisial --}}
                <div class="navbar__avatar" title="{{ $userName }}">
                    {{ strtoupper(substr($userName, 0, 1)) }}
                </div>
                
                {{-- Nama & Role --}}
                <div style="display: flex; flex-direction: column; line-height: 1.2;">
                    <span style="font-size: 0.85rem; font-weight: 600; color: #fff;">
                        {{ $userName }}
                    </span>
                    <span style="font-size: 0.7rem; color: #aaa; text-transform: capitalize;">
                        {{ $userRole }}
                    </span>
                </div>
            </div>

            {{-- Tombol Logout (Hanya jika login) --}}
            @auth
                <form action="{{ route('logout') }}" method="POST" style="display: inline; margin-left: 10px;">
                    @csrf
                    <button type="submit" class="navbar__link" style="background: none; border: none; cursor: pointer; color: #ef4444; padding: 8px; display: flex; align-items: center;" title="Logout">
                        <span class="material-symbols-outlined">logout</span>
                    </button>
                </form>
            @endauth

        </div>

    </nav>
</header>