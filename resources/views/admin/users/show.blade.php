<x-layout title="Detail User">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
            <p class="text-gray-600">{{ ucfirst($user->role) }} • {{ $user->email }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold mb-4">Informasi Pengguna</h2>
            <p class="border-b pb-2">NIM/NIP: {{ $user->nim_nip }}</p>
            <div class="mt-4 flex justify-end gap-2">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg">Kembali</a>
                <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Edit</a>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg" onclick="return confirm('Hapus user ini?')">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</x-layout>