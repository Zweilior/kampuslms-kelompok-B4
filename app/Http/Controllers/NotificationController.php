<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function unreadCount(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'count' => $user->unreadNotifications()->count(),
            'latest' => $user->notifications()
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn ($n) => [
                    'id' => $n->id,
                    'message' => $n->data['message'] ?? '',
                    'is_read' => $n->read_at !== null,
                    'created_at' => $n->created_at?->toIso8601String(),
                    'url' => $this->urlFor($n),
                ]),
        ]);
    }

    public function markAsRead(Request $request, string $id)
    {
        // Query dibatasi ke notifikasi milik user yang login: ID milik orang lain
        // tidak ditemukan (404), jadi keberadaannya pun tidak bocor.
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return $this->respond($request);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return $this->respond($request);
    }

    private function respond(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'count' => $request->user()->unreadNotifications()->count(),
            ]);
        }

        return back();
    }

    private function urlFor($notification): ?string
    {
        $courseId = $notification->data['course_id'] ?? null;

        if (class_basename($notification->type) === 'TugasBaru' && $courseId) {
            return route('mahasiswa.courses.assignments.index', $courseId);
        }

        return null; // frontend arahkan ke halaman daftar notifikasi
    }
}