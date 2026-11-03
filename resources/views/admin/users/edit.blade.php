<x-layout title="Edit User">
    <main class="users-edit mx-auto max-w-[640px] animate-[fadeUp_0.5s_cubic-bezier(0.4,0,0.2,1)]">
        <header class="users-edit__header mb-6">
            <span class="users-edit__eyebrow mb-1 block font-['Inter',sans-serif] text-[0.6875rem] font-semibold uppercase tracking-[0.12em] text-[var(--primary)]">Manajemen pengguna</span>
            <h1 class="users-edit__title m-0 mb-1 font-['Plus_Jakarta_Sans',sans-serif] text-[1.875rem] font-bold tracking-[-0.02em] text-[var(--text-primary)]">Edit Pengguna</h1>
            <p class="users-edit__subtitle m-0 text-sm text-[var(--text-secondary)]">Perbarui informasi akun <strong class="font-semibold text-[var(--text-primary)]">{{ $user->name }}</strong>.</p>
        </header>

        <section class="users-edit__card rounded-[var(--radius-lg)] border border-[var(--border-hairline)] bg-[var(--bg-elevated)] p-7 shadow-[var(--shadow-md)]">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group mb-5 flex flex-col gap-2">
                    <label for="name" class="form-label inline-flex items-center gap-1.5 font-['Inter',sans-serif] text-xs font-semibold uppercase tracking-[0.05em] text-[var(--text-secondary)]">Nama Lengkap</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="form-input w-full rounded-[var(--radius-md)] border border-[var(--border-hairline)] bg-[var(--bg-base)] px-4 py-3 text-sm text-[var(--text-primary)] outline-none transition focus:border-[var(--primary)] focus:bg-[var(--bg-elevated)] focus:ring-4 focus:ring-[rgba(139,174,102,0.15)]" autocomplete="name" required>
                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group mb-5 flex flex-col gap-2">
                    <label for="email" class="form-label inline-flex items-center gap-1.5 font-['Inter',sans-serif] text-xs font-semibold uppercase tracking-[0.05em] text-[var(--text-secondary)]">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="form-input w-full rounded-[var(--radius-md)] border border-[var(--border-hairline)] bg-[var(--bg-base)] px-4 py-3 text-sm text-[var(--text-primary)] outline-none transition focus:border-[var(--primary)] focus:bg-[var(--bg-elevated)] focus:ring-4 focus:ring-[rgba(139,174,102,0.15)]" autocomplete="email" required>
                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group mb-5 flex flex-col gap-2">
                    <label for="nim_nip" class="form-label inline-flex items-center gap-1.5 font-['Inter',sans-serif] text-xs font-semibold uppercase tracking-[0.05em] text-[var(--text-secondary)]">NIM/NIP</label>
                    <input id="nim_nip" type="text" name="nim_nip" value="{{ old('nim_nip', $user->nim_nip) }}"
                        class="form-input w-full rounded-[var(--radius-md)] border border-[var(--border-hairline)] bg-[var(--bg-base)] px-4 py-3 text-sm text-[var(--text-primary)] outline-none transition focus:border-[var(--primary)] focus:bg-[var(--bg-elevated)] focus:ring-4 focus:ring-[rgba(139,174,102,0.15)]" required>
                    @error('nim_nip') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group mb-5 flex flex-col gap-2">
                    <label for="role" class="form-label inline-flex items-center gap-1.5 font-['Inter',sans-serif] text-xs font-semibold uppercase tracking-[0.05em] text-[var(--text-secondary)]">Role</label>
                    <select id="role" name="role" class="form-select w-full rounded-[var(--radius-md)] border border-[var(--border-hairline)] bg-[var(--bg-base)] px-4 py-3 text-sm text-[var(--text-primary)] outline-none transition focus:border-[var(--primary)] focus:bg-[var(--bg-elevated)] focus:ring-4 focus:ring-[rgba(139,174,102,0.15)]" required>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                        <option value="dosen" @selected(old('role', $user->role) === 'dosen')>Dosen</option>
                        <option value="mahasiswa" @selected(old('role', $user->role) === 'mahasiswa')>Mahasiswa</option>
                    </select>
                    @error('role') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group mb-5 flex flex-col gap-2">
                    <label for="password" class="form-label inline-flex items-center gap-1.5 font-['Inter',sans-serif] text-xs font-semibold uppercase tracking-[0.05em] text-[var(--text-secondary)]">Password Baru (opsional)</label>
                    <input id="password" type="password" name="password" class="form-input w-full rounded-[var(--radius-md)] border border-[var(--border-hairline)] bg-[var(--bg-base)] px-4 py-3 text-sm text-[var(--text-primary)] outline-none transition focus:border-[var(--primary)] focus:bg-[var(--bg-elevated)] focus:ring-4 focus:ring-[rgba(139,174,102,0.15)]"
                        autocomplete="new-password" aria-describedby="password-hint">
                    <p id="password-hint" class="form-hint mt-1 font-['Inter',sans-serif] text-[0.6875rem] font-medium text-[var(--text-muted)]">Kosongkan jika password tidak ingin diubah.</p>
                    @error('password') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="users-edit__actions mt-7 flex items-center justify-end gap-3 border-t border-[var(--border-hairline)] pt-6">
                    <a href="{{ route('admin.users.show', $user) }}" class="users-edit__back inline-flex min-h-11 items-center justify-center rounded-[var(--radius-md)] border border-[var(--border-hairline)] bg-[var(--bg-hover)] px-[1.15rem] py-[0.7rem] font-['Plus_Jakarta_Sans',sans-serif] text-sm font-semibold text-[var(--text-primary)] no-underline transition hover:-translate-y-px hover:border-[var(--border-hover)] hover:bg-[var(--color-surface-container-high)] hover:text-[var(--primary)]">Kembali</a>
                    <button type="submit" class="users-edit__submit inline-flex min-h-11 items-center justify-center rounded-[var(--radius-md)] border border-[var(--primary)] bg-[var(--primary)] px-[1.15rem] py-[0.7rem] font-['Plus_Jakarta_Sans',sans-serif] text-sm font-semibold text-[var(--text-on-primary,#fff)] transition hover:-translate-y-px">Simpan Perubahan</button>
                </div>
            </form>
        </section>
    </main>
</x-layout>
