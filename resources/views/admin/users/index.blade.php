{{-- Bungkus dengan komponen layout, set activeNav ke 'users' agar menu navbar menyala --}}
<x-layout activeNav="users">

    {{-- Style khusus untuk halaman ini (Modal, Grid, dll) --}}
    <style>
        /* =========================================
           CSS MODAL
           ========================================= */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            padding: 20px;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 16px;
            width: 100%;
            max-width: 700px;
            max-height: 90vh;
            overflow-y: auto;
            transform: translateY(20px) scale(0.95);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8);
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .modal-overlay.active .modal-content {
            transform: translateY(0) scale(1);
        }

        .modal-content.modal-sm {
            max-width: 450px;
        }

        /* Header Modal */
        .modal-header {
            padding: 20px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #333;
            background: linear-gradient(to right, #222, #1a1a1a);
        }

        .modal-header h2 {
            font-size: 1.25rem;
            color: #fff;
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Warna Header Spesifik */
        #modal-create .modal-header {
            background: linear-gradient(to right, rgba(163, 230, 53, 0.15), transparent);
            border-bottom-color: rgba(163, 230, 53, 0.3);
        }

        #modal-create .modal-header h2 {
            color: #a3e635;
        }

        #modal-edit .modal-header {
            background: linear-gradient(to right, rgba(59, 130, 246, 0.15), transparent);
            border-bottom-color: rgba(59, 130, 246, 0.3);
        }

        #modal-edit .modal-header h2 {
            color: #60a5fa;
        }

        #modal-detail .modal-header {
            background: linear-gradient(to right, rgba(168, 85, 247, 0.15), transparent);
            border-bottom-color: rgba(168, 85, 247, 0.3);
        }

        #modal-detail .modal-header h2 {
            color: #c084fc;
        }

        #modal-delete .modal-header {
            justify-content: center;
            background: linear-gradient(to bottom, rgba(239, 68, 68, 0.15), transparent);
            border-bottom: none;
            padding-bottom: 0;
        }

        .modal-close {
            background: rgba(255, 255, 255, 0.05);
            border: none;
            color: #888;
            cursor: pointer;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .modal-body {
            padding: 28px;
        }

        /* Form Grid */
        .modal-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .modal-form-group {
            margin-bottom: 0;
        }

        .modal-form-group.full-width {
            grid-column: span 2;
        }

        .modal-form-group label {
            display: block;
            margin-bottom: 8px;
            color: #ccc;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .modal-form-group input,
        .modal-form-group select {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #444;
            background: #222;
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .modal-form-group input:focus,
        .modal-form-group select:focus {
            outline: none;
            border-color: #a3e635;
            background: #2a2a2a;
            box-shadow: 0 0 0 3px rgba(163, 230, 53, 0.1);
        }

        /* Detail Grid */
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .detail-item {
            background: #222;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            transition: border-color 0.2s;
        }

        .detail-item:hover {
            border-color: #444;
        }

        .detail-item.full-width {
            grid-column: span 2;
        }

        .detail-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #888;
            font-weight: 600;
        }

        .detail-value {
            font-size: 1rem;
            color: #fff;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
        }

        .detail-value.role-badge {
            display: inline-block;
            width: fit-content;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            text-transform: capitalize;
        }

        /* Badge Role */
        .role-badge--admin {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .role-badge--dosen {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .role-badge--mahasiswa {
            background: rgba(168, 85, 247, 0.2);
            color: #c084fc;
            border: 1px solid rgba(168, 85, 247, 0.3);
        }

        /* Footer Modal */
        .modal-footer {
            padding: 20px 28px;
            background: #161616;
            border-top: 1px solid #333;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            border-radius: 0 0 16px 16px;
        }

        .modal-footer.center {
            justify-content: center;
        }

        .btn-secondary {
            background: #2a2a2a;
            color: #ccc;
            padding: 10px 20px;
            border-radius: 8px;
            border: 1px solid #444;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background: #333;
            color: #fff;
            border-color: #555;
        }

        /* Tombol Aksi */
        .btn-action-create {
            background: #a3e635;
            color: #000;
            border: none;
        }

        .btn-action-create:hover {
            background: #84cc16;
        }

        .btn-action-edit {
            background: #3b82f6;
            color: #fff;
            border: none;
        }

        .btn-action-edit:hover {
            background: #2563eb;
        }

        .btn-action-delete {
            background: #ef4444;
            color: #fff;
            border: none;
        }

        .btn-action-delete:hover {
            background: #dc2626;
        }

        /* Responsive */
        @media (max-width: 600px) {

            .modal-form-grid,
            .detail-grid {
                grid-template-columns: 1fr;
            }

            .modal-form-group.full-width,
            .detail-item.full-width {
                grid-column: span 1;
            }

            .modal-content {
                margin: 10px;
            }
        }

        /* =========================================
           CONTAINER UTAMA & TABEL (DIPERBARUI)
           ========================================= */
        .users-container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 0 32px;
        }

        .users-index__header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1.5rem;
            margin-bottom: 1.875rem;
        }

        .users-index__eyebrow {
            color: var(--primary);
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .users-index__title {
            margin: 0.3125rem 0 0.625rem;
            color: var(--text-primary);
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.1;
        }

        .users-index__subtitle {
            margin: 0;
            color: var(--text-secondary);
        }

        .users-index__count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            min-height: 38px;
            padding: 0 14px;
            border: 1px solid var(--border-hairline);
            border-radius: 999px;
            background: var(--bg-elevated);
            color: var(--text-secondary);
            font-size: 0.8rem;
            font-weight: 600;
            line-height: 1;
            white-space: nowrap;
        }

        /* Style untuk Filter & Search Toolbar */
        .filter-search-wrapper {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .filter-search-wrapper .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .filter-search-wrapper .input-group span {
            position: absolute;
            left: 10px;
            color: #666;
            font-size: 1rem;
            pointer-events: none;
        }

        .filter-search-wrapper input,
        .filter-search-wrapper select {
            background: #222;
            border: 1px solid #444;
            color: #fff;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            outline: none;
            transition: all 0.2s;
            height: 38px;
            /* Samakan tingginya */
        }

        .filter-search-wrapper input {
            padding-left: 34px;
            /* Ruang untuk icon search */
            width: 220px;
        }

        .filter-search-wrapper input:focus,
        .filter-search-wrapper select:focus {
            border-color: #a3e635;
            background: #2a2a2a;
        }

        .filter-search-wrapper input::placeholder {
            color: #666;
        }

        @media (max-width: 768px) {
            .users-container {
                padding: 0 1rem;
            }

            .users-index__header {
                align-items: flex-start;
                flex-direction: column;
                margin-bottom: 1.25rem;
            }

            .users-index__title {
                font-size: 2rem;
            }

            .filter-search-wrapper {
                width: 100%;
                flex-wrap: wrap;
            }

            .filter-search-wrapper select {
                flex: 1 1 8rem;
            }

            .filter-search-wrapper .input-group {
                flex: 1 1 12rem;
            }

            .filter-search-wrapper input {
                width: 100%;
            }
        }

        /* Style Tabel Profesional */
        /* Style Tabel Profesional */
        .users-table th {
            padding: 16px 24px;
            color: #a3e635;
            ;
            /* Ubah warna teks menjadi putih terang */
            font-size: 0.8rem;
            /* Sedikit lebih besar dari 0.75rem */
            font-weight: 800;
            /* Bold maksimal */
            text-transform: uppercase;
            letter-spacing: 0.08em;
            /* Spasi antar huruf lebih lebar */
            background: linear-gradient(to right, #222, #1a1a1a);
            border-bottom: 2px solid #444;
            /* Garis bawah lebih tebal */
        }

        .users-table td {
            padding: 16px 24px;
            vertical-align: middle;
            border-bottom: 1px solid #2a2a2a;
            color: #ccc;
            font-size: 0.9rem;
        }

        .users-table tbody tr {
            transition: background-color 0.2s;
        }

        .users-table tbody tr:hover {
            background-color: #222;
        }

        /* Lebar Kolom Spesifik */
        .col-id {
            width: 80px;
        }

        .col-name {
            width: 20%;
        }

        .col-email {
            width: 25%;
        }

        .col-nim {
            width: 15%;
        }

        .col-role {
            width: 120px;
        }

        .col-action {
            width: 280px;
            text-align: right;
        }

        /* Tombol Aksi di Tabel */
        .table-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
            text-decoration: none;
        }

        .btn-detail {
            background: rgba(168, 85, 247, 0.1);
            color: #c084fc;
            border-color: rgba(168, 85, 247, 0.3);
        }

        .btn-detail:hover {
            background: rgba(168, 85, 247, 0.2);
        }

        .btn-edit {
            background: rgba(59, 130, 246, 0.1);
            color: #60a5fa;
            border-color: rgba(59, 130, 246, 0.3);
        }

        .btn-edit:hover {
            background: rgba(59, 130, 246, 0.2);
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            border-color: rgba(239, 68, 68, 0.3);
        }

        .btn-delete:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        .users-modal.modal-overlay {
            position: fixed;
            z-index: 2000;
            inset: 0;
            width: 100%;
            height: 100%;
            min-height: 100vh;
            min-height: 100dvh;
            overflow-y: auto;
            padding: max(1rem, env(safe-area-inset-top)) max(1rem, env(safe-area-inset-right))
                max(1rem, env(safe-area-inset-bottom)) max(1rem, env(safe-area-inset-left));
            background: rgba(5, 9, 5, 0.72);
            backdrop-filter: blur(9px);
            -webkit-backdrop-filter: blur(9px);
        }

        .users-modal .modal-content {
            width: min(100%, 43rem);
            max-height: calc(100vh - 2rem);
            max-height: calc(100dvh - 2rem);
            margin: auto;
            border-color: var(--border-hairline);
            border-radius: 1.1rem;
            background: var(--bg-elevated);
            box-shadow: 0 1.5rem 4rem rgba(0, 0, 0, 0.48), 0 0 2rem var(--glow-sage);
        }

        .users-modal .modal-header,
        .users-modal .modal-footer {
            flex: 0 0 auto;
        }

        .users-modal form {
            display: flex;
            min-height: 0;
            flex: 1 1 auto;
            flex-direction: column;
            overflow: hidden;
        }

        .users-modal .modal-body {
            min-height: 0;
            overflow-y: auto;
        }

        .users-modal form .modal-body {
            flex: 1 1 auto;
        }

        #modal-detail.modal-overlay {
            align-items: center;
        }

        #modal-detail .modal-content {
            width: min(100%, 38rem);
        }

        #modal-detail .modal-header {
            flex: 0 0 auto;
            padding: 1rem 1.25rem;
            border-bottom-color: var(--border-hairline);
            background: linear-gradient(110deg, var(--bg-hover), var(--bg-elevated));
        }

        #modal-detail .modal-header h2 {
            color: var(--text-primary);
            font-size: 1.1rem;
        }

        #modal-detail .modal-header h2 .material-symbols-outlined {
            color: var(--primary);
        }

        #modal-detail .modal-body {
            min-height: 0;
            overflow-y: auto;
            padding: 1.1rem 1.25rem;
        }

        #modal-detail .detail-grid {
            gap: 0.75rem;
        }

        #modal-detail .detail-item {
            gap: 0.35rem;
            padding: 0.85rem 1rem;
            border-color: var(--border-hairline);
            border-radius: 0.8rem;
            background: var(--bg-hover);
        }

        #modal-detail .detail-label {
            color: var(--text-muted);
            font-size: 0.68rem;
            letter-spacing: 0.08em;
        }

        #modal-detail .detail-value {
            color: var(--text-primary);
            font-size: 0.92rem;
            overflow-wrap: anywhere;
        }

        #modal-detail .detail-value.role-badge {
            padding: 0.25rem 0.7rem;
            font-size: 0.8rem;
        }

        #modal-detail .modal-footer {
            flex: 0 0 auto;
            padding: 0.8rem 1.25rem;
            border-top-color: var(--border-hairline);
            background: var(--bg-hover);
        }

        #modal-detail .btn-secondary {
            min-width: 5rem;
            background: var(--bg-elevated);
            color: var(--text-primary);
            border-color: var(--border-hairline);
        }

        #modal-detail .btn-secondary:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        @media (max-width: 600px) {
            .users-modal.modal-overlay {
                padding: max(0.65rem, env(safe-area-inset-top)) max(0.65rem, env(safe-area-inset-right))
                    max(0.65rem, env(safe-area-inset-bottom)) max(0.65rem, env(safe-area-inset-left));
            }

            .users-modal .modal-content {
                max-height: calc(100vh - 1.3rem);
                max-height: calc(100dvh - 1.3rem);
            }

            #modal-detail .modal-header,
            #modal-detail .modal-body,
            #modal-detail .modal-footer {
                padding-inline: 1rem;
            }
        }
    </style>

    {{-- KONTEN UTAMA --}}
    <div class="users-container">

        {{-- Header Halaman --}}
        <div class="users-index__header">
            <div class="users-index__title-group">
                <span class="users-index__eyebrow">Manajemen Pengguna</span>
                <h1 class="users-index__title">Daftar User</h1>
                <p class="users-index__subtitle">Kelola akun admin, dosen, dan mahasiswa di sini.</p>
            </div>

            <button type="button" class="btn btn--primary" onclick="openCreateModal()"
                style="background: #a3e635; color: #000; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <span class="material-symbols-outlined">person_add</span>
                Tambah User
            </button>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="alert alert--success"
                style="background: rgba(163, 230, 53, 0.1); border: 1px solid #a3e635; color: #a3e635; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <span class="material-symbols-outlined">check_circle</span>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert--error" role="alert"
                style="background: rgba(220, 80, 72, 0.1); border: 1px solid rgba(220, 80, 72, 0.4); color: #e77d76; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <span class="material-symbols-outlined" aria-hidden="true">error</span>
                {{ session('error') }}
            </div>
        @endif

        {{-- Card Tabel --}}
        <div class="users-index__card"
            style="background: #1a1a1a; border: 1px solid #333; border-radius: 16px; overflow: hidden;">

            {{-- Card Header dengan Search & Filter --}}
            <div class="users-index__card-header"
                style="padding: 20px 24px; border-bottom: 1px solid #333; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">

                {{-- Kiri: Judul --}}
                <h2 class="users-index__card-title"
                    style="color: #fff; margin: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined" style="color: #a3e635;">group</span>
                    Daftar Pengguna
                </h2>

                {{-- Filter di kiri, pencarian dan jumlah hasil di kanan --}}
                <form method="GET" action="{{ route('admin.users.index') }}" class="filter-search-wrapper" data-live-search data-live-target="#usersTable tbody" data-live-count=".users-index__count" data-live-pagination=".users-pagination">
                    {{-- Dropdown Filter Role --}}
                    <select id="roleFilter" name="role" aria-label="Filter berdasarkan role">
                        <option value="">Semua Role</option>
                        <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                        <option value="dosen" @selected(request('role') === 'dosen')>Dosen</option>
                        <option value="mahasiswa" @selected(request('role') === 'mahasiswa')>Mahasiswa</option>
                    </select>

                    {{-- Input Pencarian --}}
                    <div class="input-group">
                        <span class="material-symbols-outlined" aria-hidden="true">search</span>
                        <input type="search" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, NIM...">
                    </div>

                    {{-- Badge Count --}}
                    <span class="users-index__count">
                        {{ number_format($users->total()) }} user
                    </span>
                    <span class="search-feedback" data-live-feedback role="status" aria-live="polite"></span>
                </form>
            </div>

            <div class="users-table-wrapper" style="overflow-x: auto;">
                <table class="users-table" style="width: 100%; border-collapse: collapse;" id="usersTable">
                    <thead>
                        <tr>
                            {{-- Semua header di tengah --}}
                            <th class="col-id" style="text-align: center;">No</th>
                            <th class="col-name" style="text-align: center;">Nama</th>
                            <th class="col-email" style="text-align: center;">Email</th>
                            <th class="col-nim" style="text-align: center;">NIM/NIP</th>
                            <th class="col-role" style="text-align: center;">Role</th>
                            <th class="col-action" style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            {{-- Tambahkan data-attribute untuk filtering --}}
                            <tr class="user-row" data-name="{{ strtolower($user->name) }}"
                                data-email="{{ strtolower($user->email) }}"
                                data-nim="{{ strtolower($user->nim_nip ?? '') }}"
                                data-role="{{ strtolower($user->role) }}">

                                {{-- Semua isi data di tengah (kecuali kolom Aksi) --}}
                                <td class="col-id" style="color: #888; font-family: monospace; text-align: center;">
                                    {{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="col-name" style="color: #fff; font-weight: 500; text-align: center;">
                                    {{ $user->name }}
                                </td>
                                <td class="col-email" style="color: #aaa; text-align: center;">
                                    {{ $user->email }}
                                </td>
                                <td class="col-nim" style="color: #aaa; font-family: monospace; text-align: center;">
                                    {{ $user->nim_nip ?? '—' }}
                                </td>
                                <td class="col-role" style="text-align: center;">
                                    <span class="role-badge role-badge--{{ $user->role }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="col-action" style="text-align: right;">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        {{-- Tombol Detail --}}
                                        <button type="button" class="table-action-btn btn-detail"
                                            onclick="openDetailModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ $user->nim_nip ?? '-' }}', '{{ $user->role }}')">
                                            <span class="material-symbols-outlined"
                                                style="font-size: 1rem;">visibility</span> Detail
                                        </button>

                                        {{-- Tombol Edit --}}
                                        <button type="button" class="table-action-btn btn-edit"
                                            onclick="openEditModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ $user->nim_nip ?? '' }}', '{{ $user->role }}')">
                                            <span class="material-symbols-outlined" style="font-size: 1rem;">edit</span>
                                            Edit
                                        </button>

                                        {{-- Tombol Hapus --}}
                                        <button type="button" class="table-action-btn btn-delete"
                                            onclick="openDeleteModal('{{ $user->id }}', '{{ addslashes($user->name) }}')">
                                            <span class="material-symbols-outlined"
                                                style="font-size: 1rem;">delete</span> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 40px; text-align: center; color: #666;">
                                    <span class="material-symbols-outlined"
                                        style="font-size: 3rem; display: block; margin-bottom: 10px;">group_off</span>
                                    {{ request()->filled('search') || request()->filled('role') ? 'Tidak ada user yang cocok dengan pencarian atau filter.' : 'Belum ada user yang terdaftar.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="users-pagination" style="display: flex; justify-content: center; padding: 12px 24px; border-top: 1px solid #333;">
                {{ $users->links('vendor.pagination.custom-pagination') }}
            </div>
        </div>
    </div>

    <div id="modal-create" class="modal-overlay users-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><span class="material-symbols-outlined">person_add</span> Tambah User Baru</h2>
                <button class="modal-close" onclick="closeModal('modal-create')"><span
                        class="material-symbols-outlined">close</span></button>
            </div>
            <form id="form-create" method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="modal-form-grid">
                        <div class="modal-form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="name" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="modal-form-group">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="contoh@kampuslms.test" required>
                        </div>
                        <div class="modal-form-group">
                            <label>Password</label>
                            <input type="password" name="password" placeholder="Minimal 8 karakter" required>
                        </div>
                        <div class="modal-form-group">
                            <label>NIM/NIP</label>
                            <input type="text" name="nim_nip" placeholder="Contoh: ADM001 / NIM001">
                        </div>
                        <div class="modal-form-group full-width">
                            <label>Role</label>
                            <select name="role" required>
                                <option value="" disabled selected>-- Pilih Role --</option>
                                <option value="admin">Admin</option>
                                <option value="dosen">Dosen</option>
                                <option value="mahasiswa">Mahasiswa</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modal-create')">Batal</button>
                    <button type="submit" class="btn btn--primary btn-action-create">Simpan User</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL DETAIL USER                          --}}
    {{-- ========================================== --}}
    <div id="modal-detail" class="modal-overlay users-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><span class="material-symbols-outlined">badge</span> Detail User</h2>
                <button class="modal-close" onclick="closeModal('modal-detail')"><span
                        class="material-symbols-outlined">close</span></button>
            </div>
            <div class="modal-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">ID User</span>
                        <span class="detail-value" id="detail-id"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Role</span>
                        <span class="detail-value role-badge" id="detail-role"></span>
                    </div>
                    <div class="detail-item full-width">
                        <span class="detail-label">Nama Lengkap</span>
                        <span class="detail-value" id="detail-name"></span>
                    </div>
                    <div class="detail-item full-width">
                        <span class="detail-label">Email</span>
                        <span class="detail-value" id="detail-email"></span>
                    </div>
                    <div class="detail-item full-width">
                        <span class="detail-label">NIM / NIP</span>
                        <span class="detail-value" id="detail-nim"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-detail')">Tutup</button>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL EDIT USER                            --}}
    {{-- ========================================== --}}
    <div id="modal-edit" class="modal-overlay users-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><span class="material-symbols-outlined">edit</span> Edit User</h2>
                <button class="modal-close" onclick="closeModal('modal-edit')"><span
                        class="material-symbols-outlined">close</span></button>
            </div>
            <form id="form-edit" method="POST" action="">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="modal-form-grid">
                        <div class="modal-form-group">
                            <label>Nama</label>
                            <input type="text" name="name" id="edit-name" required>
                        </div>
                        <div class="modal-form-group">
                            <label>Email</label>
                            <input type="email" name="email" id="edit-email" required>
                        </div>
                        <div class="modal-form-group">
                            <label>Password Baru <span
                                    style="font-size: 0.75rem; color:#888;">(Opsional)</span></label>
                            <input type="password" name="password" placeholder="••••••••">
                        </div>
                        <div class="modal-form-group">
                            <label>NIM/NIP</label>
                            <input type="text" name="nim_nip" id="edit-nim">
                        </div>
                        <div class="modal-form-group full-width">
                            <label>Role</label>
                            <select name="role" id="edit-role">
                                <option value="admin">Admin</option>
                                <option value="dosen">Dosen</option>
                                <option value="mahasiswa">Mahasiswa</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('modal-edit')">Batal</button>
                    <button type="submit" class="btn btn--primary btn-action-edit">Update User</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL HAPUS USER                           --}}
    {{-- ========================================== --}}
    <div id="modal-delete" class="modal-overlay users-modal">
        <div class="modal-content modal-sm">
            <div class="modal-header">
                <span class="material-symbols-outlined" style="font-size: 3rem; color: #ef4444;">warning</span>
            </div>
            <div class="modal-body" style="text-align: center; padding-top: 10px;">
                <h2 style="color: #fff; margin-bottom: 10px; font-family: 'Plus Jakarta Sans', sans-serif;">Hapus User?
                </h2>
                <p style="color: #aaa; font-size: 0.95rem;">Apakah Anda yakin ingin menghapus <br><strong
                        id="delete-name"
                        style="color:#fff; font-size: 1.1rem; display: block; margin-top: 8px;"></strong></p>
                <p style="color: #ef4444; font-size: 0.8rem; margin-top: 15px;">Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <form id="form-delete" method="POST" action="">
                @csrf @method('DELETE')
                <div class="modal-footer center">
                    <button type="button" class="btn-secondary" onclick="closeModal('modal-delete')">Batal</button>
                    <button type="submit" class="btn btn--primary btn-action-delete">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- JAVASCRIPT LOGIC                           --}}
    {{-- ========================================== --}}
    <script>
        // --- Logic Modal ---
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal.parentElement !== document.body) {
                document.body.append(modal);
            }
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            document.body.style.overflow = '';
        }

        const userRouteBase = @json(route('admin.users.index'));

        function openCreateModal() {
            document.getElementById('form-create').reset();
            openModal('modal-create');
        }

        function openDetailModal(id, name, email, nim, role) {
            document.getElementById('detail-id').innerText = '#' + id.padStart(3, '0');
            document.getElementById('detail-name').innerText = name;
            document.getElementById('detail-email').innerText = email;
            document.getElementById('detail-nim').innerText = nim;
            const roleEl = document.getElementById('detail-role');
            roleEl.innerText = role;
            roleEl.className = 'detail-value role-badge role-badge--' + role.toLowerCase();
            openModal('modal-detail');
        }

        function openEditModal(id, name, email, nim, role) {
            const form = document.getElementById('form-edit');
            form.action = `${userRouteBase}/${id}`;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-nim').value = nim;
            document.getElementById('edit-role').value = role;
            openModal('modal-edit');
        }

        function openDeleteModal(id, name) {
            const form = document.getElementById('form-delete');
            form.action = `${userRouteBase}/${id}`;
            document.getElementById('delete-name').innerText = name;
            openModal('modal-delete');
        }
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) closeModal(this.id);
            });
        });

    </script>

</x-layout>
