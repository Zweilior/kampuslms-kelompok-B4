<x-layout title="Materi - Dosen">
    <div class="max-w-5xl mx-auto px-4">
        <div class="flex justify-between mb-6">
            <h1 class="text-2xl font-bold">Materi Pemrograman Web</h1>
            <a href="{{ route('dosen.materials.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg">+ Upload Materi</a>
        </div>
        <div class="bg-white rounded-lg shadow divide-y">
            <div class="p-4 flex justify-between items-center">
                <div class="flex gap-3 items-center">
                    <span class="material-symbols-outlined text-red-500">picture_as_pdf</span>
                    <span>Pertemuan 1 - Pengenalan HTML</span>
                </div>
                <div>
                    <a href="{{ route('dosen.materials.edit', 1) }}" class="text-yellow-600 mr-3 hover:underline">Edit</a>
                    <form action="#" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:underline">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>