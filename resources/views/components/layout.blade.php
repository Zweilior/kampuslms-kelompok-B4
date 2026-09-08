{{-- 
    Layout utama aplikasi.
    Komponen ini dibuat agar struktur HTML seperti navbar dan halaman dasar
    tidak perlu ditulis ulang pada setiap view.
--}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    {{-- Membuat tampilan responsif pada perangkat dengan ukuran berbeda. --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Judul halaman dapat dikirim dari view menggunakan atribut title. --}}
    <title>{{ $title ?? 'Kampus LMS' }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f4f6f8;
        }

        nav {
            background: #1f2937;
            padding: 15px 30px;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
        }

        main {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .course {
            background: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        .course a {
            text-decoration: none;
        }
    </style>
</head>

<body>

    {{-- 
        Navigasi utama.
        route() digunakan agar tautan bergantung pada nama route,
        bukan URL yang ditulis secara hardcode.
    --}}
    <nav>
        <a href="{{ route('courses.index') }}">Mata Kuliah</a>
    </nav>

    {{-- 
        $slot berisi isi dari view yang menggunakan <x-layout>.
        Slot digunakan karena Blade component tidak memakai @extends/@section.
    --}}
    <main>
        {{ $slot }}
    </main>

</body>
</html>