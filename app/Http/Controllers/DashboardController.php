<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $stats = Cache::remember('dashboard:admin:stats', now()->addMinutes(10), function (): array {
            $userCounts = User::query()
                ->selectRaw('role, COUNT(*) as total')
                ->groupBy('role')
                ->pluck('total', 'role');

            return [
                'totalUsers' => (int) $userCounts->sum(),
                'totalLecturers' => (int) ($userCounts['dosen'] ?? 0),
                'totalStudents' => (int) ($userCounts['mahasiswa'] ?? 0),
                'totalCourses' => Course::count(),
            ];
        });

        return view('admin.dashboard', $stats);
    }

    public function dosenDashboard()
    {
        $lecturerId = auth()->id();
        $stats = Cache::remember(
            "dashboard:dosen:{$lecturerId}:stats",
            now()->addMinute(),
            function () use ($lecturerId): array {
                return [
                    'totalCourses' => Course::where('lecturer_id', $lecturerId)->count(),
                    'openAssignments' => Assignment::whereHas('course', function ($query) use ($lecturerId) {
                        $query->where('lecturer_id', $lecturerId);
                    })
                        ->where('status', 'published')
                        ->where('due_at', '>=', now())
                        ->count(),
                    'ungradedSubmissions' => Submission::whereHas('assignment.course', function ($query) use ($lecturerId) {
                        $query->where('lecturer_id', $lecturerId);
                    })
                        ->whereHas('assignment', fn ($query) => $query
                            ->where('status', 'published')
                            ->where('due_at', '<=', now()))
                        ->whereDoesntHave('grade')
                        ->count(),
                ];
            }
        );
        $gradingAssignments = Assignment::query()
            ->with('course')
            ->withCount([
                'submissions as ungraded_submissions_count' => fn ($query) => $query->whereDoesntHave('grade'),
            ])
            ->whereHas('course', fn ($query) => $query->where('lecturer_id', $lecturerId))
            ->where('status', 'published')
            ->where('due_at', '<=', now())
            ->whereHas('submissions', fn ($query) => $query->whereDoesntHave('grade'))
            ->orderBy('due_at')
            ->limit(5)
            ->get();

        return view('dosen.dashboard', $stats + compact('gradingAssignments'));
    }
}
