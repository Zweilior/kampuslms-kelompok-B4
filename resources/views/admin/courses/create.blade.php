<x-layout title="Tambah Mata Kuliah">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Tambah Mata Kuliah</h2>
            <form action="#" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Kode MK</label>
                    <input type="text" name="code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nama MK</label>
                    <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">SKS</label>
                    <input type="number" name="sks" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Dosen Pengampu</label>
                    <select name="dosen_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="1">Budi Santoso</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.courses.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg">Batal</a>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-layout>