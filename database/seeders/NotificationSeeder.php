<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Data contoh notifikasi untuk akun demo.
 * Aman dijalankan berulang: notifikasi lama akun demo dihapus dulu.
 *
 *   php artisan db:seed --class=NotificationSeeder
 */
class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa@kampuslms.test')->first();
        $dosen = User::where('email', 'dosen@kampuslms.test')->first();

        if ($mahasiswa) {
            $mahasiswa->notifications()->delete();

            $this->make($mahasiswa, 'TugasBaru', 'Tugas baru telah diterbitkan.', null);
            $this->make($mahasiswa, 'NilaiDiberikan', 'Tugas Anda telah dinilai.', null);
            $this->make($mahasiswa, 'PengingatTenggat', 'Tenggat tugas tinggal 2 hari lagi.', now()->subDay());
        }

        if ($dosen) {
            $dosen->notifications()->delete();

            $this->make($dosen, 'PengumpulanBaru', 'Ada pengumpulan tugas baru dari mahasiswa.', null);
            $this->make($dosen, 'PengumpulanBaru', 'Ada pengumpulan tugas baru dari mahasiswa.', now()->subHours(3));
        }
    }

    private function make(User $user, string $type, string $message, $readAt): void
    {
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\' . $type,
            'data' => ['message' => $message],
            'read_at' => $readAt,
        ]);
    }
}
