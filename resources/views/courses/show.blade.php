{{-- 
    Halaman detail satu mata kuliah.
    View ini menerima data $course dari CourseController@show.
--}}

<x-layout title="Detail Mata Kuliah">

    {{-- Menampilkan judul halaman. --}}
    <h1>Detail Mata Kuliah</h1>

    <div class="course">

        {{-- Menampilkan kode mata kuliah. --}}
        <h2>{{ $course['code'] }}</h2>

        {{-- Menampilkan nama mata kuliah. --}}
        <p>Nama Mata Kuliah: {{ $course['name'] }}</p>

        {{-- Menampilkan dosen pengampu. --}}
        <p>Dosen: {{ $course['lecturer'] }}</p>

        {{-- Menampilkan semester mata kuliah. --}}
        <p>Semester: {{ $course['semester'] }}</p>

    </div>

    {{-- 
        Kembali ke halaman daftar menggunakan nama route.
        Tidak menggunakan href="/courses" karena semua tautan
        harus menggunakan helper route().
    --}}
    <a href="{{ route('courses.index') }}">
        Kembali ke Daftar Mata Kuliah
    </a>

</x-layout>