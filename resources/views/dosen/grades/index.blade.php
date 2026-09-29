<x-layout title="Penilaian - Dosen">
    <div class="max-w-5xl mx-auto px-4">
        <h1 class="text-2xl font-bold mb-6">Input Nilai - Pemrograman Web</h1>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="px-6 py-4">Andi Saputra</td>
                        <td class="px-6 py-4">12345678</td>
                        <td class="px-6 py-4"><input type="number" class="border rounded px-2 py-1 w-20" value="85"></td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-blue-600 hover:underline">Simpan</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-layout>