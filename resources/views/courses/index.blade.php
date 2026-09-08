{{-- 
    Halaman daftar mata kuliah.
    View ini menerima data $courses dari CourseController@index.
--}}

<x-layout title="Daftar Mata Kuliah">

    {{-- Menampilkan judul halaman daftar mata kuliah. --}}
    <h1>Daftar Mata Kuliah</h1>

    {{-- 
        Melakukan perulangan untuk menampilkan setiap mata kuliah.
        Data berasal dari array statis yang dikirim oleh controller.
    --}}
    @foreach ($courses as $course)
        <div class="course">

            {{-- Menampilkan kode dan nama mata kuliah. --}}
            <h2>{{ $course['code'] }} - {{ $course['name'] }}</h2>

            {{-- Menampilkan dosen pengampu. --}}
            <p>Dosen: {{ $course['lecturer'] }}</p>

            {{-- Menampilkan semester mata kuliah. --}}
            <p>Semester: {{ $course['semester'] }}</p>

            {{-- 
                route() digunakan untuk membuat link menuju halaman detail.
                ID mata kuliah dikirim ke parameter {course}.
            --}}
            <a href="{{ route('courses.show', $course['id']) }}">
                Lihat Detail
            </a>

        </div>
    @endforeach

</x-layout>