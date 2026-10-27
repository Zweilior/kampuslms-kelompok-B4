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

    public function open(Request $request, string $id)
    {
        // Query dibatasi ke notifikasi milik user yang login (mencegah IDOR)
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $url = $this->urlFor($notification) ?? route('notifications.index');

        return redirect($url);
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
        $type = class_basename($notification->type);
        $data = $notification->data;
        $courseId = $data['course_id'] ?? null;
        $assignmentId = $data['assignment_id'] ?? null;
        $submissionId = $data['submission_id'] ?? null;

        // Cari course_id atau assignment_id jika salah satunya belum ada di data payload
        if (! $courseId && $assignmentId) {
            $assignment = \App\Models\Assignment::find($assignmentId);
            $courseId = $assignment?->course_id;
        }

        if (! $assignmentId && $submissionId) {
            $submission = \App\Models\Submission::find($submissionId);
            $assignmentId = $submission?->assignment_id;
            $courseId = $courseId ?? $submission?->assignment?->course_id;
        }

        $user = auth()->user() ?? $notification->notifiable;
        $isMahasiswa = $user?->role === 'mahasiswa';
        $isDosen = $user?->role === 'dosen';

        // 1. Jika course_id dan assignment_id tersedia lengkap
        if ($courseId && $assignmentId) {
            if ($isDosen) {
                return route('dosen.courses.assignments.submissions.index', [$courseId, $assignmentId]);
            }
            if ($type === 'TugasBaru') {
                return route('mahasiswa.courses.assignments.index', $courseId);
            }
            return route('mahasiswa.courses.assignments.submissions.index', [$courseId, $assignmentId]);
        }

        // 2. Jika hanya ada course_id
        if ($courseId) {
            if ($type === 'TugasBaru' || $type === 'PengingatTenggat' || str_contains(strtolower($data['message'] ?? ''), 'tugas')) {
                return $isMahasiswa
                    ? route('mahasiswa.courses.assignments.index', $courseId)
                    : route('dosen.courses.assignments.index', $courseId);
            }

            return $isDosen
                ? route('dosen.courses.show', $courseId)
                : route('mahasiswa.courses.show', $courseId);
        }

        // 3. Fallback cerdas untuk notifikasi lama / data dummy tanpa ID referensi
        $message = strtolower($data['message'] ?? '');

        // Terkait Nilai
        if ($type === 'NilaiDiberikan' || str_contains($message, 'dinilai') || str_contains($message, 'nilai')) {
            return $isMahasiswa ? route('mahasiswa.grades.index') : route('dosen.grades.index');
        }

        // Terkait Pengumpulan
        if ($type === 'PengumpulanBaru' || str_contains($message, 'mengumpulkan')) {
            return $isDosen ? route('dosen.grades.index') : null;
        }

        // Terkait Tugas atau Tenggat
        if ($type === 'TugasBaru' || $type === 'PengingatTenggat' || str_contains($message, 'tugas') || str_contains($message, 'tenggat')) {
            if ($isMahasiswa) {
                $firstCourse = $user?->courses()->first();
                return $firstCourse
                    ? route('mahasiswa.courses.assignments.index', $firstCourse->id)
                    : route('mahasiswa.courses.index');
            }
            if ($isDosen) {
                $firstCourse = $user?->taughtCourses()->first();
                return $firstCourse
                    ? route('dosen.courses.assignments.index', $firstCourse->id)
                    : route('dosen.courses.index');
            }
        }

        // 4. Default fallback aman
        if ($isMahasiswa) {
            return route('mahasiswa.dashboard');
        }
        if ($isDosen) {
            return route('dosen.dashboard');
        }

        return null;
    }
}