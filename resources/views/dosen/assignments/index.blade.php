<x-layout title="Tugas - Dosen">
    <div class="max-w-5xl mx-auto px-4">
        <div class="flex justify-between mb-6">
            <h1 class="text-2xl font-bold">Daftar Tugas</h1>
            <a href="{{ route('dosen.courses.assignments.create', 1) }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg">+ Buat Tugas</a>
        </div>
        <div class="bg-white rounded-lg shadow divide-y">
            @forelse($assignments as $assignment)
            <div class="p-4 flex justify-between items-center">
                <div>
                    <p class="font-bold">{{ $assignment->title }}</p>
                    <p class="text-sm text-gray-500">Deadline: {{ $assignment->deadline }}</p>
                    <p class="text-xs text-gray-400">{{ $assignment->course->name }}</p>
                </div>
                <div>
                    <a href="{{ route('dosen.courses.assignments.show', [$assignment->course->id, $assignment->id]) }}" 
                       class="text-blue-600 mr-3 hover:underline">Lihat Submission</a>
                    <a href="{{ route('dosen.courses.assignments.edit', [$assignment->course->id, $assignment->id]) }}" 
                       class="text-yellow-600 mr-3 hover:underline">Edit</a>
                    <form action="{{ route('dosen.courses.assignments.destroy', [$assignment->course->id, $assignment->id]) }}" 
                          method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:underline" 
                                onclick="return confirm('Hapus tugas?')">Hapus</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">Belum ada tugas.</div>
            @endforelse
        </div>
    </div>
</x-layout>