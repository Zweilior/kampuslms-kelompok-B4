<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function dashboard()
    {
        $studentId = auth()->id();
        $courses = auth()->user()->courses()
            ->where('courses.status', 'active')
            ->with('lecturer')
            ->orderBy('courses.code')
            ->get();
        $courseIds = $courses->modelKeys();
        $assignments = Assignment::query()
            ->whereIn('course_id', $courseIds)
            ->where('status', 'published');
        $upcomingAssignments = (clone $assignments)
            ->where('due_at', '>=', now())
            ->whereDoesntHave('submissions', fn ($query) => $query->where('user_id', $studentId))
            ->with('course')
            ->orderBy('due_at')
            ->limit(5)
            ->get();
        $pendingAssignmentCount = (clone $assignments)
            ->whereDoesntHave('submissions', fn ($query) => $query->where('user_id', $studentId))
            ->count();
        $gradedAssignmentCount = (clone $assignments)
            ->whereHas('submissions', fn ($query) => $query
                ->where('user_id', $studentId)
                ->whereHas('grade'))
            ->count();

        return view('mahasiswa.dashboard', compact(
            'courses',
            'upcomingAssignments',
            'pendingAssignmentCount',
            'gradedAssignmentCount'
        ));
    }

    public function courses()
    {
        $courses = auth()->user()->courses()
            ->where('courses.status', 'active')
            ->with('lecturer')
            ->orderBy('code')
            ->get();

        return view('mahasiswa.courses.index', compact('courses'));
    }

    public function monitoringGrades(Request $request)
    {
        $validated = $request->validate([
            'status' => ['sometimes', 'in:active,archived'],
        ]);
        $studentId = auth()->id();
        $status = $validated['status'] ?? 'active';
        $studentCourses = auth()->user()->courses();
        $courses = (clone $studentCourses)
            ->where('courses.status', $status)
            ->with(['finalGrades' => fn ($query) => $query->where('user_id', $studentId)])
            ->orderBy('courses.code')
            ->get();
        $activeCourseCount = (clone $studentCourses)->where('courses.status', 'active')->count();
        $archivedCourseCount = (clone $studentCourses)->where('courses.status', 'archived')->count();

        $gradePoints = [
            'A' => 4,
            'A-' => 3.75,
            'AB' => 3.5,
            'B+' => 3.25,
            'B' => 3,
            'B-' => 2.75,
            'BC' => 2.5,
            'C+' => 2.25,
            'C' => 2,
            'D' => 1,
            'E' => 0,
        ];
        $gradedCourses = $courses->filter(fn ($course) => $course->finalGrades->isNotEmpty());
        $gradedCredits = $gradedCourses->sum(fn ($course) => $course->sks);
        $weightedGradePoints = $gradedCourses->sum(function ($course) use ($gradePoints) {
            $finalGrade = $course->finalGrades->first();
            $gradePoint = $gradePoints[strtoupper($finalGrade->letter_grade)] ?? null;

            return $gradePoint === null ? 0 : $gradePoint * $course->sks;
        });
        $ips = $gradedCredits > 0 ? $weightedGradePoints / $gradedCredits : null;

        return view('mahasiswa.grades.index', compact(
            'courses',
            'status',
            'activeCourseCount',
            'archivedCourseCount',
            'ips'
        ));
    }

    public function showCourse(Course $course)
    {
        $course->load('lecturer');

        return view('mahasiswa.courses.show', compact('course'));
    }

    public function assignments(Course $course)
    {
        $assignments = $course->assignments()
            ->where('status', 'published')
            ->with(['submissions' => fn ($query) => $query
                ->where('user_id', auth()->id())
                ->latest()])
            ->orderBy('due_at')
            ->get();

        return view('mahasiswa.assignments.index', compact(
            'course',
            'assignments'
        ));
    }

    public function materials(Course $course)
    {
        $materials = $course->materials()
            ->latest()
            ->get();

        return view('mahasiswa.materials.index', compact(
            'course',
            'materials'
        ));
    }

    public function submissions(Course $course, Assignment $assignment)
    {
        $submissions = $assignment->submissions()
            ->where('user_id', auth()->id())
            ->with('grade')
            ->latest()
            ->get()
            ->each(function (Submission $submission) {
                $submission->setAttribute('score', $submission->grade?->score);
            });

        return view('mahasiswa.submissions.index', compact(
            'course',
            'assignment',
            'submissions'
        ));
    }

    public function grades(Course $course)
    {
        $studentId = auth()->id();
        $assignments = $course->assignments()
            ->where('status', 'published')
            ->with([
                'gradeComponent',
                'submissions' => fn ($query) => $query
                    ->where('user_id', $studentId)
                    ->with('grade')
                    ->latest(),
            ])
            ->orderBy('due_at')
            ->get();
        $finalGrade = $course->finalGrades()
            ->where('user_id', $studentId)
            ->first();
        $totalWeight = $course->gradeComponents()->sum('weight');

        return view('mahasiswa.grades.show', compact(
            'course',
            'assignments',
            'finalGrade',
            'totalWeight'
        ));
    }

    public function createSubmission(Course $course, Assignment $assignment)
    {
        return view('mahasiswa.submissions.create', compact(
            'course',
            'assignment'
        ));
    }
}
