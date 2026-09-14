<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail User</title>
</head>
<body>

    <h1>Detail User</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <p>
        <strong>ID:</strong>
        {{ $user->id }}
    </p>

    <p>
        <strong>Nama:</strong>
        {{ $user->name }}
    </p>

    <p>
        <strong>Email:</strong>
        {{ $user->email }}
    </p>

    <p>
        <strong>NIM/NIP:</strong>
        {{ $user->nim_nip ?? '-' }}
    </p>

    <p>
        <strong>Role:</strong>
        {{ $user->role }}
    </p>

    <hr>

    <a href="{{ route('users.index') }}">
        Kembali ke Daftar User
    </a>

    <a href="{{ route('users.edit', $user) }}">
        Edit User
    </a>

    <form
        action="{{ route('users.destroy', $user) }}"
        method="POST"
        style="display: inline;"
    >
        @csrf
        @method('DELETE')

        <button type="submit">
            Hapus User
        </button>
    </form>

</body>
</html>