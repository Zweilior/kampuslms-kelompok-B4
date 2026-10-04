<x-layout title="Buat Tugas">
    <div class="max-w-3xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Buat Tugas Baru</h2>
            <form action="{{ route('dosen.courses.assignments.store', $course) }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label>Judul</label>
                    <input name="title"
                        value="{{ old('title') }}"
                        class="mt-1 block w-full rounded-md border-gray-300"
                        required>
                </div>

                <div class="mb-4">
                    <label>Deadline</label>
                    <input type="datetime-local"
                        name="due_at"
                        value="{{ old('due_at') }}"
                        class="mt-1 block w-full rounded-md border-gray-300"
                        required>
                </div>

                <div class="mb-4">
                    <label>Instruksi</label>
                    <textarea name="instructions"
                            class="mt-1 block w-full rounded-md border-gray-300">{{ old('instructions') }}</textarea>
                </div>

                <div class="mb-4">
                    <label>Nilai Maksimal</label>
                    <input type="number"
                        name="max_score"
                        value="{{ old('max_score', 100) }}"
                        min="0"
                        max="255"
                        class="mt-1 block w-full rounded-md border-gray-300"
                        required>
                </div>

                <div class="mb-4">
                    <label>Izinkan terlambat</label>
                    <select name="allow_late"
                            class="mt-1 block w-full rounded-md border-gray-300">
                        <option value="1">Ya</option>
                        <option value="0">Tidak</option>
                    </select>
                </div>

                <input type="hidden" name="status" value="published">

                <div class="flex justify-end gap-2">
                    <a href="{{ route('dosen.courses.assignments.index', $course) }}"
                    class="px-4 py-2 bg-gray-200 rounded">
                        Batal
                    </a>

                    <button class="px-4 py-2 bg-blue-600 text-white rounded">
                        Buat Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>