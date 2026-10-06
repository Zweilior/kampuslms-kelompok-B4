@include('errors.layout', [
    'statusCode' => '404',
    'pageTitle' => $exception->getMessage() ?: 'Halaman Tidak Ditemukan',
    'heading' => 'Halaman yang Anda Cari Tidak Ada',
    'message' => 'Maaf, halaman yang Anda tuju tidak dapat ditemukan. Mungkin URL-nya salah ketik, atau halaman tersebut sudah dipindahkan.',
    'primaryUrl' => route('courses.index'),
    'primaryLabel' => 'Ke Daftar Mata Kuliah',
])
