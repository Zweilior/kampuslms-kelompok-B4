<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Daftar putih untuk data pengguna.
 *
 * Tidak pernah mengeluarkan: password, remember_token, deleted_at, dll.
 *
 * Aturan privasi:
 *  - id, name, role   : selalu tampil
 *  - nim_nip          : pemilik akun, admin, atau dosen (perlu untuk menilai)
 *  - email            : hanya pemilik akun dan admin
 *
 * Jadi mahasiswa yang melihat dosen di CourseResource tidak mendapat
 * email dosen, dan dosen yang melihat submission tidak mendapat email mahasiswa.
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $viewer = $request->user();

        $isSelf  = $viewer && (int) $viewer->id === (int) $this->id;
        $isAdmin = $viewer && $viewer->role === 'admin';
        $isDosen = $viewer && $viewer->role === 'dosen';

        return [
            'id'      => $this->id,
            'name'    => $this->name,
            'role'    => $this->role,
            'nim_nip' => $this->when($isSelf || $isAdmin || $isDosen, $this->nim_nip),
            'email'   => $this->when($isSelf || $isAdmin, $this->email),
        ];
    }
}
