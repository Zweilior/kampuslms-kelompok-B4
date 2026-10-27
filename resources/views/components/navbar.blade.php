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
        default     => 'login',
    };
    $coursesRoute = match ($userRole) {
        'admin'     => 'admin.courses.index',
        'dosen'     => 'dosen.courses.index',
        'mahasiswa' => 'mahasiswa.courses.index',
        default     => 'login',
    };

    // Penanda menu aktif
    $activeNav = match (true) {
        request()->routeIs('admin.users.*')       => 'users',
        request()->routeIs('admin.courses.*')     => 'courses',
        request()->routeIs('admin.materials.*')   => 'materials',
        request()->routeIs('admin.assignments.*') => 'assignments',
        request()->routeIs('admin.grades.*')      => 'grades',

        request()->routeIs('dosen.dashboard')                              => 'dashboard',
        request()->routeIs('dosen.courses.*')                              => 'courses',
        request()->routeIs('dosen.grades.*')                               => 'grades',

        request()->routeIs('mahasiswa.grades.*') => 'grades',
        request()->routeIs('mahasiswa.courses.*') => 'courses',

        request()->routeIs('admin.dashboard', 'dosen.dashboard', 'mahasiswa.dashboard') => 'dashboard',
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
            ['path' => 'dashboard', 'icon' => 'grid_view',   'label' => 'Dashboard', 'route' => 'dosen.dashboard'],
            ['path' => 'courses',   'icon' => 'menu_book',   'label' => 'Mata Kuliah', 'route' => 'dosen.courses.index'],
            ['path' => 'grades',    'icon' => 'grade',       'label' => 'Nilai', 'route' => 'dosen.grades.index'],
        ];
    }

    // ==================== MENU KHUSUS MAHASISWA ====================
    if ($userRole === 'mahasiswa') {
        $navItems = [
            ['path' => 'dashboard', 'icon' => 'grid_view', 'label' => 'Dashboard',        'route' => 'mahasiswa.dashboard'],
            ['path' => 'courses',   'icon' => 'menu_book', 'label' => 'Mata Kuliah',      'route' => 'mahasiswa.courses.index'],
            ['path' => 'grades',    'icon' => 'grade',     'label' => 'Monitoring Nilai', 'route' => 'mahasiswa.grades.index'],
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

            @if ($userRole !== 'admin')
                {{-- Notifications --}}
                <div class="navbar__notifications"
                x-data="{
                    open: false,
                    count: 0,
                    notifications: [],
                    poller: null,
                    init() {
                        this.fetchNotifications();
                        this.poller = setInterval(() => this.fetchNotifications(), 30000);
                    },
                    destroy() {
                        clearInterval(this.poller);
                    },
                    formatDate(value) {
                        if (!value) {
                            return '';
                        }

                        return new Date(value).toLocaleString('id-ID', {
                            dateStyle: 'short',
                            timeStyle: 'short',
                        });
                    },
                    async fetchNotifications() {
                        try {
                            const response = await fetch('{{ route('notifications.unread-count') }}', {
                                headers: { Accept: 'application/json' },
                            });

                            if (!response.ok) {
                                throw new Error(`Notification request failed: ${response.status}`);
                            }

                            const payload = await response.json();
                            if (!Number.isInteger(payload.count) || !Array.isArray(payload.latest)) {
                                throw new Error('Invalid notification response contract');
                            }

                            this.count = payload.count;
                            this.notifications = payload.latest.slice(0, 5);
                        } catch (error) {
                            console.error('Unable to load notifications from the database.', error);
                        }
                    },
                    async markAsRead(notification) {
                        if (notification.is_read) {
                            return;
                        }

                        try {
                            const response = await fetch(`{{ url('/notifications') }}/${notification.id}/read`, {
                                method: 'POST',
                                headers: {
                                    Accept: 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                            });

                            if (!response.ok) {
                                throw new Error(`Mark as read failed: ${response.status}`);
                            }

                            notification.is_read = true;
                            this.count = Math.max(0, this.count - 1);
                        } catch (error) {
                            console.error('Unable to mark notification as read.', error);
                        }
                    },
                    async markAllAsRead() {
                        if (this.count === 0) {
                            return;
                        }

                        try {
                            const response = await fetch('{{ route('notifications.read-all') }}', {
                                method: 'POST',
                                headers: {
                                    Accept: 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                            });

                            if (!response.ok) {
                                throw new Error(`Mark all as read failed: ${response.status}`);
                            }

                            this.notifications.forEach(notification => notification.is_read = true);
                            this.count = 0;
                        } catch (error) {
                            console.error('Unable to mark all notifications as read.', error);
                        }
                    }
                }"
                @click.outside="open = false"
                @keydown.escape.window="open = false">
                <button type="button" class="navbar__notification-button" title="Notifikasi"
                    aria-label="Buka notifikasi" :aria-expanded="open"
                    @click="open = !open">
                    <span class="material-symbols-outlined" aria-hidden="true">notifications</span>
                    <span class="navbar__notification-badge" x-show="count > 0" x-text="count > 99 ? '99+' : count"
                        x-cloak></span>
                </button>

                <div class="navbar__notification-dropdown" x-show="open" x-transition x-cloak>
                    <div class="navbar__notification-header">
                        <h2>Notifikasi</h2>
                        <button type="button" class="navbar__notification-mark-all"
                            x-show="count > 0" @click="markAllAsRead()">
                            Tandai semua dibaca
                        </button>
                    </div>

                    <div class="navbar__notification-list">
                        <template x-for="notification in notifications" :key="notification.id">
                            <article class="navbar__notification-item" :class="{ 'is-unread': !notification.is_read }">
                                <a class="navbar__notification-content"
                                    :href="'{{ url('/notifications') }}/' + notification.id + '/open'">
                                    <span class="navbar__notification-message" x-text="notification.message || 'Notifikasi baru'"></span>
                                    <time class="navbar__notification-time" x-text="formatDate(notification.created_at)"></time>
                                </a>
                                <button type="button" class="navbar__notification-read"
                                    x-show="!notification.is_read" @click="markAsRead(notification)">
                                    Tandai dibaca
                                </button>
                            </article>
                        </template>
                        <p class="navbar__notification-empty" x-show="notifications.length === 0">
                            Belum ada notifikasi.
                        </p>
                    </div>

                    <a href="{{ route('notifications.index') }}" class="navbar__notification-all">
                        Lihat semua
                    </a>
                </div>
                </div>
            @endif

            {{-- User Info --}}
            <div class="navbar__user-info">
                <div class="navbar__avatar" title="{{ $userName }}">
                    {{ strtoupper(substr($userName, 0, 1)) }}
                </div>
                <div class="navbar__user-details">
                    <span class="navbar__user-name">
                        {{ $userName }}
                    </span>
                    <span class="navbar__user-role">
                        {{ $userRole }}
                    </span>
                </div>
            </div>

            @auth
                <div x-data="{ logoutOpen: false }" @keydown.escape.window="logoutOpen = false">
                    <button type="button" class="navbar__link navbar__logout-button"
                        style="background: none; border: none; cursor: pointer; padding: 8px; display: flex; align-items: center;"
                        title="Keluar" aria-label="Keluar dari akun"
                        @click="logoutOpen = true">
                        <span class="material-symbols-outlined">logout</span>
                    </button>

                    <template x-teleport="body">
                        <div class="logout-dialog-backdrop" x-cloak x-show="logoutOpen"
                            x-transition.opacity @click.self="logoutOpen = false"
                            role="presentation">
                            <section class="logout-dialog logout-dialog--confirmation" role="dialog" aria-modal="true"
                                aria-labelledby="logout-dialog-title"
                                @click.stop>
                                <h2 class="logout-dialog__title" id="logout-dialog-title">
                                    Yakin ingin keluar?
                                </h2>
                                <p class="logout-dialog__message">
                                    Anda akan keluar dari akun {{ $userName }}. Pastikan perubahan Anda sudah tersimpan.
                                </p>
                                <div class="logout-dialog__actions">
                                    <button type="button" class="logout-dialog__button"
                                        @click="logoutOpen = false">
                                        Batal
                                    </button>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="logout-dialog__button logout-dialog__button--confirm">
                                            Ya, keluar
                                        </button>
                                    </form>
                                </div>
                            </section>
                        </div>
                    </template>
                </div>
            @endauth

        </div>

    </nav>
</header>