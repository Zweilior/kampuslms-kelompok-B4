<x-layout title="Mata Kuliah Saya">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold mb-6">Mata Kuliah yang Diampu</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg">
                <h3 class="text-lg font-bold">Pemrograman Web</h3>
                <p class="text-sm text-gray-500">IF-2023-A • 3 SKS</p>
                <a href="{{ route('dosen.courses.show', 1) }}" class="text-blue-600 text-sm mt-3 inline-block hover:underline">Kelola Kelas →</a>
            </div>
        </div>
    </div>
</x-layout>