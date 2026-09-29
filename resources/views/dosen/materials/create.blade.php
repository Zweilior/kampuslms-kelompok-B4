<x-layout title="Upload Materi">
    <div class="max-w-3xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Upload Materi Baru</h2>
            <form action="#" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium">Judul Materi</label>
                    <input type="text" name="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Deskripsi</label>
                    <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">File Materi</label>
                    <input type="file" name="file" class="mt-1 block w-full text-sm" required>
                    <p class="text-xs text-red-500 mt-1">*File akan disimpan di storage private.</p>
                </div>
                <div class="flex justify-end">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">Upload</button>
                </div>
            </form>
        </div>
    </div>
</x-layout>