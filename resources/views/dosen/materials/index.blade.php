<x-layout title="Materi - Dosen">
    <div class="max-w-5xl mx-auto px-4">
        <div class="flex justify-between mb-6">
            <h1 class="text-2xl font-bold">Materi {{ $course->name }}</h1>
            <a href="{{ route('dosen.courses.materials.create', $course) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg">+ Upload Materi</a>
        </div>

        <div class="bg-white rounded-lg shadow divide-y">
            @forelse($materials as $material)
            <div class="p-4 flex justify-between items-center">
                <div class="flex gap-3 items-center">
                    <span class="material-symbols-outlined text-red-500">
                        picture_as_pdf
                    </span>
                    <span>{{ $material->title }}</span>
                </div>

                <div>
                    <a href="{{ route('dosen.courses.materials.edit', [$course, $material]) }}"
                       class="text-yellow-600 mr-3 hover:underline">
                        Edit
                    </a>

                    <form action="{{ route('dosen.courses.materials.destroy', [$course, $material]) }}"
                          method="POST"
                          class="inline">
                        @csrf
                        @method('DELETE')

                        <button class="text-red-600 hover:underline"
                                onclick="return confirm('Hapus materi?')">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">
                Belum ada materi.
            </div>
            @endforelse
        </div>
    </div>
</x-layout>