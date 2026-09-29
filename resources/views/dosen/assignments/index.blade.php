<x-layout title="Tugas - Dosen">
    <div class="max-w-5xl mx-auto px-4">
        <div class="flex justify-between mb-6">
            <h1 class="text-2xl font-bold">Daftar Tugas</h1>
            <a href="{{ route('dosen.assignments.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg">+ Buat Tugas</a>
        </div>
        <div class="bg-white rounded-lg shadow divide-y">
            <div class="p-4 flex justify-between items-center">
                <div>
                    <p class="font-bold">Tugas 1: Portofolio</p>
                    <p class="text-sm text-gray-500">Deadline: 20 Okt 2023</p>
                </div>
                <div>
                    <a href="{{ route('dosen.assignments.show', 1) }}" class="text-blue-600 mr-3 hover:underline">Lihat Submission</a>
                    <a href="{{ route('dosen.assignments.edit', 1) }}" class="text-yellow-600 mr-3 hover:underline">Edit</a>
                    <form action="#" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:underline">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>