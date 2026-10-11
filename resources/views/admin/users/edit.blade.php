<x-layout title="Edit User">
    <main class="users-edit">
        <header class="users-edit__header">
            <span class="users-edit__eyebrow">Manajemen pengguna</span>
            <h1 class="users-edit__title">Edit Pengguna</h1>
            <p class="users-edit__subtitle">Perbarui informasi akun <strong>{{ $user->name }}</strong>.</p>
        </header>

        <section class="users-edit__card">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="form-input" autocomplete="name" required>
                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="form-input" autocomplete="email" required>
                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="nim_nip" class="form-label">NIM/NIP</label>
                    <input id="nim_nip" type="text" name="nim_nip" value="{{ old('nim_nip', $user->nim_nip) }}"
                        class="form-input" required>
                    @error('nim_nip') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="role" class="form-label">Role</label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                        <option value="dosen" @selected(old('role', $user->role) === 'dosen')>Dosen</option>
                        <option value="mahasiswa" @selected(old('role', $user->role) === 'mahasiswa')>Mahasiswa</option>
                    </select>
                    @error('role') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password Baru (opsional)</label>
                    <input id="password" type="password" name="password" class="form-input"
                        autocomplete="new-password" aria-describedby="password-hint">
                    <p id="password-hint" class="form-hint">Kosongkan jika password tidak ingin diubah.</p>
                    @error('password') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="users-edit__actions">
                    <a href="{{ route('admin.users.show', $user) }}" class="users-edit__back">Kembali</a>
                    <button type="submit" class="users-edit__submit">Simpan Perubahan</button>
                </div>
            </form>
        </section>
    </main>
</x-layout>
