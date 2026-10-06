@include('errors.layout', [
    'statusCode' => '401',
    'pageTitle' => 'Perlu Login',
    'heading' => 'Anda Perlu Login Terlebih Dahulu',
    'message' => 'Sesi Anda mungkin telah berakhir atau Anda belum masuk. Silakan login untuk melanjutkan ke halaman yang dituju.',
    'primaryUrl' => route('login'),
    'primaryLabel' => 'Login',
])
