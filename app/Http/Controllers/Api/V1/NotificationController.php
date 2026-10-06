<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationCollection;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * GET /api/v1/notifications
     *
     * Hanya notifikasi milik user yang sedang login (disaring di level query,
     * terbaru lebih dulu).
     */
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->paginate(15);

        return new NotificationCollection($notifications);
    }

    /**
     * POST /api/v1/notifications/{id}/read
     *
     * Titik rawan IDOR: id notifikasi (UUID) dikirim klien, jadi
     * kepemilikan WAJIB dicek sebelum ditandai dibaca.
     * Aman dipanggil berulang (markAsRead hanya mengisi read_at kalau masih kosong).
     */
    public function markAsRead(Request $request, string $id)
    {
        $notification = Notification::findOrFail($id);   // tidak ada -> 404

        $user = $request->user();

        if (
            $notification->notifiable_type !== $user->getMorphClass() ||
            (int) $notification->notifiable_id !== (int) $user->id
        ) {
            abort(403);                                   // milik orang lain -> 403
        }

        $notification->markAsRead();

        return response()->json([
            'data' => new NotificationResource($notification),
        ]);
    }
}
