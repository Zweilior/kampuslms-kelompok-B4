<x-layout title="Edit Tugas - Dosen">
    <div class="max-w-3xl mx-auto px-4">
        <h1 class="text-2xl font-bold mb-6">Edit Tugas</h1>

        <form action="{{ route('dosen.courses.assignments.update', [$course, $assignment]) }}"
              method="POST"
              class="bg-white rounded-lg shadow p-6">

            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block font-medium mb-2">Judul Tugas</label>
                <input type="text"
                       name="title"
                       value="{{ old('title', $assignment->title) }}"
                       class="w-full border rounded-lg px-3 py-2"
                       required>
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-2">Deadline</label>
                <input type="datetime-local"
                       name="due_at"
                       value="{{ old('due_at', $assignment->due_at?->format('Y-m-d\TH:i')) }}"
                       class="w-full border rounded-lg px-3 py-2"
                       required>
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-2">Instruksi</label>
                <textarea name="instructions"
                          rows="5"
                          class="w-full border rounded-lg px-3 py-2">{{ old('instructions', $assignment->instructions) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-2">Nilai Maksimal</label>
                <input type="number"
                       name="max_score"
                       value="{{ old('max_score', $assignment->max_score) }}"
                       min="0"
                       max="255"
                       class="w-full border rounded-lg px-3 py-2"
                       required>
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-2">Boleh Terlambat?</label>

                <select name="allow_late"
                        class="w-full border rounded-lg px-3 py-2"
                        required>
                    <option value="1" {{ old('allow_late', $assignment->allow_late) == 1 ? 'selected' : '' }}>
                        Ya
                    </option>
                    <option value="0" {{ old('allow_late', $assignment->allow_late) == 0 ? 'selected' : '' }}>
                        Tidak
                    </option>
                </select>
            </div>

            <input type="hidden" name="status" value="{{ $assignment->status }}">

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                    Simpan Perubahan
                </button>

                <a href="{{ route('dosen.courses.assignments.index', $course) }}"
                   class="px-4 py-2 rounded-lg border">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-layout>