<x-layout title="Kelola Enrollment">
    <div class="max-w-5xl mx-auto px-4">
        <div class="flex justify-between mb-6">
            <h1 class="text-2xl font-bold">Enrollment - Pemrograman Web</h1>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">+ Tambah Mahasiswa</button>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="px-6 py-4">Andi Saputra</td>
                        <td class="px-6 py-4">12345678</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-red-600 hover:underline">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-layout>