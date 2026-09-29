<x-layout title="Edit Materi">
    <div class="max-w-3xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Edit Materi</h2>
            <form action="#" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium">Judul Materi</label>
                    <input type="text" name="title" value="Pertemuan 1 - Pengenalan HTML" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Ganti File (opsional)</label>
                    <input type="file" name="file" class="mt-1 block w-full text-sm">
                </div>
                <div class="flex justify-end">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">Update</button>
                </div>
            </form>
        </div>
    </div>
</x-layout>