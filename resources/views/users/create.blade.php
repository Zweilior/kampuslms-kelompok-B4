<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User</title>
</head>
<body>

    <h1>Tambah User</h1>

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div>
            <label>Nama</label>
            <input type="text" name="name">
        </div>

        <br>

        <div>
            <label>Email</label>
            <input type="email" name="email">
        </div>

        <br>

        <div>
            <label>Password</label>
            <input type="password" name="password">
        </div>

        <br>

        <div>
            <label>NIM/NIP</label>
            <input type="text" name="nim_nip">
        </div>

        <br>

        <div>
            <label>Role</label>
            <select name="role">
                <option value="admin">Admin</option>
                <option value="dosen">Dosen</option>
                <option value="mahasiswa">Mahasiswa</option>
            </select>
        </div>

        <br>

        <button type="submit">Simpan</button>
        <a href="{{ route('users.index') }}">Kembali</a>
    </form>

</body>
</html>