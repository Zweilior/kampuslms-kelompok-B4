@include('errors.layout', [
    'statusCode' => '419',
    'pageTitle' => 'Sesi Kedaluwarsa',
    'heading' => 'Sesi Halaman Telah Berakhir',
    'message' => 'Sesi Anda telah kedaluwarsa karena tidak ada aktivitas dalam waktu lama atau token keamanan tidak valid. Silakan muat ulang halaman atau masuk kembali.',
    'primaryUrl' => route('login'),
    'primaryLabel' => 'Masuk Kembali',
])
