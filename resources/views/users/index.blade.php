<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar User</title>
</head>
<body>

    <h1>Daftar User</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <p>
        <a href="{{ route('users.create') }}">+ Tambah User</a>
    </p>

    <table border="1" cellpadding="8">
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
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->nim_nip ?? '-' }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        <a href="{{ route('users.show', $user) }}">
                            Detail
                        </a>

                        <a href="{{ route('users.edit', $user) }}">
                            Edit
                        </a>

                        <form
                            action="{{ route('users.destroy', $user) }}"
                            method="POST"
                            style="display:inline;"
                        >
                            @csrf
                            @method('DELETE')

                            <button type="submit">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        Belum ada user.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>