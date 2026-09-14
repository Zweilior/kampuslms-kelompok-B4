<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
</head>
<body>

    <h1>Edit User</h1>

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label>Nama</label>
            <input
                type="text"
                name="name"
                value="{{ $user->name }}"
            >
        </div>

        <br>

        <div>
            <label>Email</label>
            <input
                type="email"
                name="email"
                value="{{ $user->email }}"
            >
        </div>

        <br>

        <div>
            <label>Password Baru</label>
            <input
                type="password"
                name="password"
                placeholder="Kosongkan jika tidak ingin mengubah"
            >
        </div>

        <br>

        <div>
            <label>NIM/NIP</label>
            <input
                type="text"
                name="nim_nip"
                value="{{ $user->nim_nip }}"
            >
        </div>

        <br>

        <div>
            <label>Role</label>
            <select name="role">
                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>
                    Admin
                </option>

                <option value="dosen" {{ $user->role === 'dosen' ? 'selected' : '' }}>
                    Dosen
                </option>

                <option value="mahasiswa" {{ $user->role === 'mahasiswa' ? 'selected' : '' }}>
                    Mahasiswa
                </option>
            </select>
        </div>

        <br>

        <button type="submit">Update</button>

        <a href="{{ route('users.show', $user) }}">
            Batal
        </a>
    </form>

</body>
</html>