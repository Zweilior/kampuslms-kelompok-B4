<x-layout title="Manajemen User - Admin">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Pengguna</h1>
            <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <span class="material-symbols-outlined">add</span> Tambah User
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach(['Admin Utama', 'Dosen Budi', 'Mahasiswa Andi'] as $i => $user)
                    <tr>
                        <td class="px-6 py-4">{{ $user }}</td>
                        <td class="px-6 py-4">user{{ $i }}@kampus.ac.id</td>
                        <td class="px-6 py-4">
                            <span class="px-2 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                {{ ['Admin','Dosen','Mahasiswa'][$i] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('admin.users.show', $i) }}" class="text-blue-600 hover:underline mr-3">Detail</a>
                            <a href="{{ route('admin.users.edit', $i) }}" class="text-indigo-600 hover:underline mr-3">Edit</a>
                            <form action="#" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline" onclick="return confirm('Hapus user?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layout>