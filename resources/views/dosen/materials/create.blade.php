<x-layout title="Tambah Materi - Dosen">
    <div class="max-w-3xl mx-auto px-4">
        <h1 class="text-2xl font-bold mb-6">Tambah Materi</h1>

        <form action="{{ route('dosen.courses.materials.store', $course) }}"
              method="POST"
              enctype="multipart/form-data"
              class="bg-white rounded-lg shadow p-6">

            @csrf

            <div class="mb-4">
                <label class="block font-medium mb-2">Judul Materi</label>
                <input type="text"
                       name="title"
                       value="{{ old('title') }}"
                       class="w-full border rounded-lg px-3 py-2"
                       required>
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-2">Deskripsi</label>
                <textarea name="description"
                          rows="5"
                          class="w-full border rounded-lg px-3 py-2">{{ old('description') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-2">Tipe Materi</label>

                <select name="type"
                        class="w-full border rounded-lg px-3 py-2"
                        required>
                    <option value="file" {{ old('type') === 'file' ? 'selected' : '' }}>
                        File
                    </option>
                    <option value="link" {{ old('type') === 'link' ? 'selected' : '' }}>
                        Link
                    </option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-2">URL Eksternal</label>
                <input type="url"
                       name="external_url"
                       value="{{ old('external_url') }}"
                       class="w-full border rounded-lg px-3 py-2"
                       placeholder="https://...">
            </div>

            <div class="mb-6">
                <label class="block font-medium mb-2">File Materi</label>
                <input type="file"
                       name="file"
                       class="w-full border rounded-lg px-3 py-2">
                <p class="text-sm text-gray-500 mt-1">
                    Maksimal ukuran file 20 MB.
                </p>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                    Simpan Materi
                </button>

                <a href="{{ route('dosen.courses.materials.index', $course) }}"
                   class="px-4 py-2 rounded-lg border">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-layout>