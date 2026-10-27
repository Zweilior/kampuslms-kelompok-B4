@extends('components.layout')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @php
            $unreadCount = auth()->user()->unreadNotifications()->count();
        @endphp

        <header class="lms-page-header">
            <div class="lms-page-header__copy">
                <div class="flex items-center gap-2 text-xs uppercase tracking-wider font-semibold opacity-90 mb-1">
                    <span class="material-symbols-outlined text-base">notifications</span>
                    <span>Pusat Pemberitahuan</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Daftar Notifikasi</h1>
                <p class="text-sm sm:text-base opacity-90 mt-1 text-white/90">
                    Pemberitahuan tugas baru, pengumpulan tugas mahasiswa, dan pembaruan nilai.
                </p>
            </div>

            @if ($unreadCount > 0)
                <div class="flex flex-col sm:flex-row items-end sm:items-center gap-2">
                    <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 text-xs font-semibold text-white backdrop-blur-sm">
                        <span class="w-2 h-2 rounded-full bg-amber-300"></span>
                        {{ $unreadCount }} belum dibaca
                    </span>
                    <form action="{{ route('notifications.read-all') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-white text-primary px-3.5 py-2 text-xs sm:text-sm font-semibold shadow hover:bg-white/90 transition-all">
                            <span class="material-symbols-outlined text-base">done_all</span>
                            Tandai semua dibaca
                        </button>
                    </form>
                </div>
            @endif
        </header>

        <section class="mt-6 space-y-3" aria-label="Daftar notifikasi">
            @forelse ($notifications as $n)
                @php
                    $isUnread = ! $n->read_at;
                    $message = $n->data['message'] ?? 'Pemberitahuan';
                    $type = class_basename($n->type);

                    // Penentuan icon & warna berdasarkan tipe notifikasi
                    $icon = 'notifications';
                    $iconColor = 'text-primary bg-primary/10';
                    if ($type === 'TugasBaru' || str_contains(strtolower($message), 'tugas baru')) {
                        $icon = 'assignment';
                        $iconColor = 'text-blue-600 bg-blue-100';
                    } elseif ($type === 'NilaiDiberikan' || str_contains(strtolower($message), 'dinilai')) {
                        $icon = 'verified';
                        $iconColor = 'text-amber-600 bg-amber-100';
                    } elseif ($type === 'PengumpulanBaru' || str_contains(strtolower($message), 'mengumpulkan')) {
                        $icon = 'upload_file';
                        $iconColor = 'text-emerald-600 bg-emerald-100';
                    }
                @endphp

                <article class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 sm:p-5 rounded-2xl transition-all {{ $isUnread ? 'bg-primary/[0.04] border-l-4 border-l-primary border border-outline/15 shadow-sm' : 'bg-surface-container-low border border-outline/10 opacity-85 hover:opacity-100' }}">
                    <div class="flex items-start gap-3.5 min-w-0 flex-1">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $iconColor }}">
                            <span class="material-symbols-outlined text-xl">{{ $icon }}</span>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('notifications.open', $n->id) }}"
                                   class="font-body-md {{ $isUnread ? 'font-bold text-on-surface' : 'font-medium text-on-surface' }} hover:text-primary transition-colors line-clamp-2">
                                    {{ $message }}
                                </a>

                                @if ($isUnread)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-primary/15 text-primary px-2 py-0.5 text-[11px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                        Baru
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-on-surface-variant/70 text-[11px]">
                                        Sudah dibaca
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 text-xs text-on-surface-variant mt-1">
                                <span class="material-symbols-outlined text-xs">schedule</span>
                                <span>{{ $n->created_at?->diffForHumans() }} · {{ $n->created_at?->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                        @if ($isUnread)
                            <form action="{{ route('notifications.read', $n->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1 rounded-xl border border-outline/20 bg-surface-container px-3 py-1.5 text-xs font-medium text-on-surface hover:bg-surface-container-high hover:text-primary transition"
                                        title="Tandai sudah dibaca">
                                    <span class="material-symbols-outlined text-sm">done</span>
                                    <span>Tandai dibaca</span>
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('notifications.open', $n->id) }}"
                           class="inline-flex items-center gap-1 rounded-xl bg-primary px-3 py-1.5 text-xs font-semibold text-on-primary hover:bg-primary-container transition shadow-sm"
                           title="Buka halaman terkait">
                            <span>Buka</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-outline/25 bg-surface-container-low py-16 px-4 text-center">
                    <span class="material-symbols-outlined text-5xl text-on-surface-variant/40 mb-3 inline-block">notifications_off</span>
                    <h2 class="font-headline-sm text-lg font-semibold text-on-surface">Belum ada notifikasi</h2>
                    <p class="text-sm text-on-surface-variant mt-1 max-w-md mx-auto">
                        Pemberitahuan tugas baru, pengumpulan jawaban, atau nilai yang telah diberikan akan muncul di sini.
                    </p>
                </div>
            @endforelse
        </section>

        @if ($notifications->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@endsection
