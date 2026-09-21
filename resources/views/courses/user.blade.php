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

    <style>
        /* =========================================
           CSS MODAL (Diperbarui: Warna & Layout)
           ========================================= */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.8); /* Lebih gelap */
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
            max-width: 700px; /* Ukuran standar untuk semua modal */
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

        /* Modal Khusus Hapus (Lebih Kecil) */
        .modal-content.modal-sm {
            max-width: 450px;
        }

        /* Header Berwarna */
        .modal-header {
            padding: 20px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #333;
            /* Default Header */
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
        #modal-create .modal-header h2 { color: #a3e635; }

        #modal-edit .modal-header {
            background: linear-gradient(to right, rgba(59, 130, 246, 0.15), transparent);
            border-bottom-color: rgba(59, 130, 246, 0.3);
        }
        #modal-edit .modal-header h2 { color: #60a5fa; }

        #modal-detail .modal-header {
            background: linear-gradient(to right, rgba(168, 85, 247, 0.15), transparent);
            border-bottom-color: rgba(168, 85, 247, 0.3);
        }
        #modal-detail .modal-header h2 { color: #c084fc; }

        #modal-delete .modal-header {
            justify-content: center;
            background: linear-gradient(to bottom, rgba(239, 68, 68, 0.15), transparent);
            border-bottom: none;
            padding-bottom: 0;
        }

        .modal-close {
            background: rgba(255,255,255,0.05);
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
            background: rgba(255,255,255,0.1);
            color: #fff; 
        }

        .modal-body {
            padding: 28px;
        }

        /* Form Layout Grid 2 Kolom */
        .modal-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .modal-form-group { margin-bottom: 0; }
        .modal-form-group.full-width { grid-column: span 2; }
        
        .modal-form-group label {
            display: block;
            margin-bottom: 8px;
            color: #ccc;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .modal-form-group input, .modal-form-group select {
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
        .modal-form-group input:focus, .modal-form-group select:focus {
            outline: none;
            border-color: #a3e635;
            background: #2a2a2a;
            box-shadow: 0 0 0 3px rgba(163, 230, 53, 0.1);
        }

        /* =========================================
           STYLE KHUSUS DETAIL USER (BARU)
           ========================================= */
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

        /* Warna Badge Role di Detail */
        .role-badge--admin { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
        .role-badge--dosen { background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); }
        .role-badge--mahasiswa { background: rgba(168, 85, 247, 0.2); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3); }


        /* Footer */
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

        /* Tombol Aksi Khusus */
        .btn-action-create { background: #a3e635; color: #000; border: none; }
        .btn-action-create:hover { background: #84cc16; }
        
        .btn-action-edit { background: #3b82f6; color: #fff; border: none; }
        .btn-action-edit:hover { background: #2563eb; }

        .btn-action-delete { background: #ef4444; color: #fff; border: none; }
        .btn-action-delete:hover { background: #dc2626; }

        /* Responsive */
        @media (max-width: 600px) {
            .modal-form-grid, .detail-grid { grid-template-columns: 1fr; }
            .modal-form-group.full-width, .detail-item.full-width { grid-column: span 1; }
            .modal-content { margin: 10px; }
        }
    </style>
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

                <button type="button" class="btn btn--primary" onclick="openCreateModal()">
                    <span class="material-symbols-outlined">person_add</span>
                    Tambah User
                </button>
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
                                            {{-- Tombol Detail --}}
                                            <button type="button"
                                               class="action-btn action-btn--view"
                                               onclick="openDetailModal(
                                                   '{{ $user->id }}',
                                                   '{{ addslashes($user->name) }}',
                                                   '{{ addslashes($user->email) }}',
                                                   '{{ $user->nim_nip ?? '-' }}',
                                                   '{{ $user->role }}'
                                               )">
                                                <span class="material-symbols-outlined">visibility</span>
                                                <span>Detail</span>
                                            </button>

                                            {{-- Tombol Edit --}}
                                            <button type="button"
                                               class="action-btn action-btn--edit"
                                               onclick="openEditModal(
                                                   '{{ $user->id }}',
                                                   '{{ addslashes($user->name) }}',
                                                   '{{ addslashes($user->email) }}',
                                                   '{{ $user->nim_nip ?? '' }}',
                                                   '{{ $user->role }}'
                                               )">
                                                <span class="material-symbols-outlined">edit</span>
                                                <span>Edit</span>
                                            </button>

                                            {{-- Tombol Hapus --}}
                                            <button type="button"
                                                    class="action-btn action-btn--delete"
                                                    onclick="openDeleteModal('{{ $user->id }}', '{{ addslashes($user->name) }}')">
                                                <span class="material-symbols-outlined">delete</span>
                                                <span>Hapus</span>
                                            </button>
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

    <!-- ========================================== -->
    <!-- MODAL TAMBAH USER                          -->
    <!-- ========================================== -->
    <div id="modal-create" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2><span class="material-symbols-outlined">person_add</span> Tambah User Baru</h2>
                <button class="modal-close" onclick="closeModal('modal-create')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <form id="form-create" method="POST" action="{{ route('users.store') }}">
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

    <!-- ========================================== -->
    <!-- MODAL DETAIL USER (DIPERBARUI)             -->
    <!-- ========================================== -->
    <div id="modal-detail" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2><span class="material-symbols-outlined">badge</span> Detail User</h2>
                <button class="modal-close" onclick="closeModal('modal-detail')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Menggunakan Grid Layout baru -->
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

    <!-- ========================================== -->
    <!-- MODAL EDIT USER                            -->
    <!-- ========================================== -->
    <div id="modal-edit" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2><span class="material-symbols-outlined">edit</span> Edit User</h2>
                <button class="modal-close" onclick="closeModal('modal-edit')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <form id="form-edit" method="POST" action="">
                @csrf
                @method('PUT')
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
                            <label>Password Baru <span style="font-size: 0.75rem; color:#888;">(Opsional)</span></label>
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

    <!-- ========================================== -->
    <!-- MODAL HAPUS USER                           -->
    <!-- ========================================== -->
    <div id="modal-delete" class="modal-overlay">
        <div class="modal-content modal-sm">
            <div class="modal-header">
                <span class="material-symbols-outlined" style="font-size: 3rem; color: #ef4444;">warning</span>
            </div>
            <div class="modal-body" style="text-align: center; padding-top: 10px;">
                <h2 style="color: #fff; margin-bottom: 10px; font-family: 'Plus Jakarta Sans', sans-serif;">Hapus User?</h2>
                <p style="color: #aaa; font-size: 0.95rem;">
                    Apakah Anda yakin ingin menghapus <br>
                    <strong id="delete-name" style="color:#fff; font-size: 1.1rem; display: block; margin-top: 8px;"></strong>
                </p>
                <p style="color: #ef4444; font-size: 0.8rem; margin-top: 15px;">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <form id="form-delete" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-footer center">
                    <button type="button" class="btn-secondary" onclick="closeModal('modal-delete')">Batal</button>
                    <button type="submit" class="btn btn--primary btn-action-delete">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- JAVASCRIPT LOGIC                           -->
    <!-- ========================================== -->
    <script>
        // Fungsi Buka/Tutup Modal
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            document.body.style.overflow = '';
        }

        // 0. Logic Modal Tambah User
        function openCreateModal() {
            document.getElementById('form-create').reset();
            openModal('modal-create');
        }

        // 1. Logic Modal Detail (Diperbarui untuk Badge Role)
        function openDetailModal(id, name, email, nim, role) {
            document.getElementById('detail-id').innerText = '#' + id.padStart(3, '0');
            document.getElementById('detail-name').innerText = name;
            document.getElementById('detail-email').innerText = email;
            document.getElementById('detail-nim').innerText = nim;
            
            // Set Role dengan Class Warna
            const roleEl = document.getElementById('detail-role');
            roleEl.innerText = role;
            roleEl.className = 'detail-value role-badge role-badge--' + role.toLowerCase();

            openModal('modal-detail');
        }

        // 2. Logic Modal Edit
        function openEditModal(id, name, email, nim, role) {
            const form = document.getElementById('form-edit');
            form.action = `/users/${id}`; 

            document.getElementById('edit-name').value = name;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-nim').value = nim;
            document.getElementById('edit-role').value = role;
            
            openModal('modal-edit');
        }

        // 3. Logic Modal Hapus
        function openDeleteModal(id, name) {
            const form = document.getElementById('form-delete');
            form.action = `/users/${id}`;

            document.getElementById('delete-name').innerText = name;
            
            openModal('modal-delete');
        }

        // Menutup modal jika area overlay diklik
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this.id);
                }
            });
        });
    </script>

</body>
</html>