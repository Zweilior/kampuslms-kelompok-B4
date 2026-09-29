<x-layout title="Kelola Mata Kuliah - Admin">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow p-6 mb-6 flex justify-between">
            <div>
                <h1 class="text-2xl font-bold">Pemrograman Web (IF-2023)</h1>
                <p class="text-gray-600">Dosen: Budi Santoso</p>
            </div>
            <a href="{{ route('admin.courses.index') }}" class="text-blue-600 hover:underline">← Kembali</a>
        </div>

        {{-- Section Enrollment --}}
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold">Enrollment Mahasiswa</h3>
                <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm">+ Enroll Mahasiswa</button>
            </div>
            <ul class="text-sm text-gray-700">
                <li>Andi Saputra - 12345678</li>
                <li>Budi Pratama - 12345679</li>
            </ul>
        </div>

        {{-- Section Materi --}}
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-4 border-b flex justify-between bg-gray-50">
                <h3 class="font-bold">Materi</h3>
                <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm">+ Tambah Materi</button>
            </div>
            <ul class="divide-y">
                <li class="p-4 flex justify-between">
                    <span>Pertemuan 1 - Pengenalan HTML</span>
                    <div>
                        <button class="text-yellow-600 mr-2 text-sm">Edit</button>
                        <button class="text-red-600 text-sm">Hapus</button>
                    </div>
                </li>
            </ul>
        </div>

        {{-- Section Tugas --}}
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-4 border-b flex justify-between bg-gray-50">
                <h3 class="font-bold">Tugas</h3>
                <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm">+ Buat Tugas</button>
            </div>
            <ul class="divide-y">
                <li class="p-4 flex justify-between">
                    <span>Tugas 1: Portofolio</span>
                    <div>
                        <button class="text-yellow-600 mr-2 text-sm">Edit</button>
                        <button class="text-red-600 text-sm">Hapus</button>
                    </div>
                </li>
            </ul>
        </div>

        {{-- Section Nilai --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="font-bold">Rekap Nilai</h3>
            </div>
            <table class="min-w-full divide-y">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs">Nama</th>
                        <th class="px-6 py-3 text-left text-xs">NIM</th>
                        <th class="px-6 py-3 text-left text-xs">Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="px-6 py-4">Andi Saputra</td>
                        <td class="px-6 py-4">12345678</td>
                        <td class="px-6 py-4 font-bold text-blue-600">A</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-layout>