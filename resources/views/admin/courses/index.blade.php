<x-layout title="Manajemen Mata Kuliah">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Daftar Mata Kuliah</h1>
            <a href="{{ route('admin.courses.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg">+ Tambah MK</a>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama MK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dosen</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="px-6 py-4">IF-2023</td>
                        <td class="px-6 py-4">Pemrograman Web</td>
                        <td class="px-6 py-4">3</td>
                        <td class="px-6 py-4">Budi Santoso</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.courses.show', 1) }}" class="text-blue-600 hover:underline mr-3">Kelola</a>
                            <a href="{{ route('admin.courses.edit', 1) }}" class="text-indigo-600 hover:underline mr-3">Edit</a>
                            <form action="#" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline" onclick="return confirm('Hapus MK?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-layout>