@include('errors.layout', [
    'statusCode' => '500',
    'pageTitle' => 'Kesalahan Server Internal',
    'heading' => 'Terjadi Kesalahan pada Server',
    'message' => 'Maaf, terjadi kendala teknis pada server kami saat memproses permintaan Anda. Tim teknis sedang mengatasinya. Silakan coba beberapa saat lagi.',
    'primaryUrl' => route('dashboard'),
    'primaryLabel' => 'Ke Dashboard',
])
