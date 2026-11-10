@include('errors.layout', [
    'statusCode' => '503',
    'pageTitle' => 'Layanan Sedang Dipelihara',
    'heading' => 'Sistem Dalam Pemeliharaan',
    'message' => 'Layanan EduSpace saat ini sedang dalam pemeliharaan berkala untuk peningkatan kualitas layanan. Kami akan segera kembali online. Silakan cek kembali beberapa saat lagi.',
    'primaryUrl' => url('/'),
    'primaryLabel' => 'Muat Ulang Halaman',
])
