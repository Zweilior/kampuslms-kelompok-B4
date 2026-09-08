{{-- 
    Halaman daftar mata kuliah.
    View ini menerima data $courses dari CourseController@index.
--}}

<x-layout title="Daftar Mata Kuliah">
-- Active: 1758509644542@@127.0.0.1@3306@kampus_db
<x-layout title="Daftar Mata Kuliah" active-nav="courses">

    {{-- HERO HEADER --}}
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-y-space-sm mb-space-lg">
        <div class="flex flex-col">
            <div class="flex items-center gap-space-xs mb-space-2xs">
                <span class="px-space-xs py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm tracking-wide uppercase">
                    Semester Aktif
                </span>
                <span class="w-1 h-1 rounded-full bg-outline"></span>
                <span class="font-label-sm text-label-sm text-on-surface-variant">{{ count($courses) }} Mata Kuliah Terdaftar</span>
            </div>
            <h1 class="font-headline-lg text-headline-lg tracking-tight">Daftar Mata Kuliah</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">
                Kelola dan pantau seluruh mata kuliah aktif beserta dosen pengampu semester ini.
            </p>
        </div>

        <a href="#"
           class="flex items-center justify-center gap-space-2xs px-space-md py-2.5 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-all duration-200 shadow-[0_0_20px_rgba(173,210,134,0.3)]">
            <span class="material-symbols-outlined text-headline-sm">add_circle</span>
            <span>Tambah Mata Kuliah</span>
        </a>
    </div>

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