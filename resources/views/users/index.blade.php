<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar User</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Material Symbols --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <div class="main-content">
        <div class="users-index">

            {{-- Header --}}
            <div class="users-index__header">
                <div class="users-index__title-group">
                    <span class="users-index__eyebrow">Manajemen Pengguna</span>
                    <h1 class="users-index__title">Daftar User</h1>
                    <p class="users-index__subtitle">
                        Kelola akun admin, dosen, dan mahasiswa di sini.
                    </p>
                </div>

                <a href="{{ route('users.create') }}" class="btn btn--primary">
                    <span class="material-symbols-outlined">person_add</span>
                    Tambah User
                </a>
            </div>

            {{-- Alert --}}
            @if (session('success'))
                <div class="alert alert--success">
                    <span class="material-symbols-outlined">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Card --}}
            <div class="users-index__card">

                <div class="users-index__card-header">
                    <h2 class="users-index__card-title">
                        <span class="material-symbols-outlined">group</span>
                        Semua Pengguna
                    </h2>
                    <span class="users-index__count">
                        {{ $users->count() }} user
                    </span>
                </div>

                <div class="users-table-wrapper">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>NIM/NIP</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td class="users-table__id">
                                        #{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="users-table__name">{{ $user->name }}</td>
                                    <td class="users-table__email">{{ $user->email }}</td>
                                    <td class="users-table__email">
                                        {{ $user->nim_nip ?? '—' }}
                                    </td>
                                    <td>
                                        <span class="role-badge role-badge--{{ $user->role }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="users-table__actions">
                                            <a href="{{ route('users.show', $user) }}"
                                               class="action-btn action-btn--view">
                                                <span class="material-symbols-outlined">visibility</span>
                                                <span>Detail</span>
                                            </a>

                                            <a href="{{ route('users.edit', $user) }}"
                                               class="action-btn action-btn--edit">
                                                <span class="material-symbols-outlined">edit</span>
                                                <span>Edit</span>
                                            </a>

                                            <form action="{{ route('users.destroy', $user) }}"
                                                  method="POST"
                                                  style="display:inline;"
                                                  onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="action-btn action-btn--delete">
                                                    <span class="material-symbols-outlined">delete</span>
                                                    <span>Hapus</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="users-table__empty">
                                        <span class="material-symbols-outlined">group_off</span>
                                        Belum ada user yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</body>
</html>