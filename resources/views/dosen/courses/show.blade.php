<x-layout title="Detail Mata Kuliah - Dosen">
    <div class="max-w-7xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h1 class="text-2xl font-bold">Pemrograman Web (IF-2023-A)</h1>
            <p class="text-gray-600">3 SKS • 30 Mahasiswa</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('dosen.enrollments.index') }}" class="bg-blue-600 text-white p-4 rounded-lg text-center hover:bg-blue-700">Enrollment</a>
            <a href="{{ route('dosen.materials.index') }}" class="bg-green-600 text-white p-4 rounded-lg text-center hover:bg-green-700">Materi</a>
            <a href="{{ route('dosen.assignments.index') }}" class="bg-yellow-600 text-white p-4 rounded-lg text-center hover:bg-yellow-700">Tugas</a>
            <a href="{{ route('dosen.grades.index') }}" class="bg-purple-600 text-white p-4 rounded-lg text-center hover:bg-purple-700">Penilaian</a>
        </div>
    </div>
</x-layout>