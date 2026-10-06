<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Dasar semua koleksi API.
 *
 * Kontrak Bagian 5 meminta meta persis:
 *   { "current_page": ..., "last_page": ..., "total": ... }
 *
 * Laravel otomatis menambahkan "meta" dan "links" lengkap untuk hasil paginate().
 * Method ini mengganti keduanya dengan tiga field sesuai kontrak.
 * Jangan menulis "meta" lagi di toArray() koleksi turunan, karena akan
 * digabung (array_merge_recursive) dan nilainya menjadi array ganda.
 */
class ApiCollection extends ResourceCollection
{
    public function paginationInformation($request, $paginated, $default): array
    {
        return [
            'meta' => [
                'current_page' => $paginated['current_page'],
                'last_page'    => $paginated['last_page'],
                'total'        => $paginated['total'],
            ],
        ];
    }
}
