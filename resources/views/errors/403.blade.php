@include('errors.layout', [
    'statusCode' => '403',
    'pageTitle' => 'Akses Ditolak',
    'heading' => 'Akses ke Halaman Ini Ditolak',
    'message' => 'Maaf, akun Anda tidak memiliki izin untuk mengakses halaman ini. Silakan kembali ke dashboard atau hubungi administrator jika Anda merasa ini keliru.',
    'primaryUrl' => route('dashboard'),
    'primaryLabel' => 'Ke Dashboard',
])
