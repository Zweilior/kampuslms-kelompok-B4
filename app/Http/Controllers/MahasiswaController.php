<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\GradeComponent;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
        $status = $validated['status'] ?? null;
        $courses = auth()->user()->courses()
            ->with(['finalGrades' => fn ($query) => $query->where('user_id', $studentId)])
            ->orderBy('courses.code')
            ->get();
        $activeCourses = $courses->where('status', 'active')->values();
        $archivedCourses = $courses->where('status', 'archived')->values();
        $activeCourseCount = $activeCourses->count();
        $archivedCourseCount = $archivedCourses->count();

        return view('mahasiswa.grades.index', compact(
            'activeCourses',
            'archivedCourses',
            'status',
            'activeCourseCount',
            'archivedCourseCount'
        ));
    }

    public function showCourse(Course $course)
    {
        Gate::authorize('view', $course);

        $course->load('lecturer');

        return view('mahasiswa.courses.show', compact('course'));
    }

    public function gradeComponents(Course $course)
    {
        abort_unless(
            auth()->user()->courses()->whereKey($course->id)->exists(),
            403,
            'Anda tidak terdaftar pada mata kuliah ini.'
        );

        $studentId = auth()->id();
        $gradeComponents = $course->gradeComponents()
            ->with([
                'assignments' => fn ($query) => $query
                    ->where('status', 'published')
                    ->with([
                        'submissions' => fn ($submissionQuery) => $submissionQuery
                            ->where('user_id', $studentId)
                            ->with('grade')
                            ->latest(),
                    ])
                    ->orderBy('due_at'),
            ])
            ->orderBy('id')
            ->get();
        $totalWeight = $gradeComponents->sum('weight');
        $assignmentCount = $gradeComponents->sum(fn (GradeComponent $component) => $component->assignments->count());

        return view('mahasiswa.grades.rubric', compact(
            'course',
            'gradeComponents',
            'totalWeight',
            'assignmentCount'
        ));
    }

    public function assignments(Course $course)
    {
        Gate::authorize('viewAny', [Assignment::class, $course]);

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
        Gate::authorize('viewAny', [Material::class, $course]);
        
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
        Gate::authorize('viewAny', [Submission::class, $assignment]);

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
        Gate::authorize('view', $course);

        $studentId = auth()->id();
        $gradeComponents = $course->gradeComponents()
            ->orderBy('name')
            ->get();
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
        $assignments->each(function (Assignment $assignment) {
            $assignment->setAttribute(
                'grade_category_label',
                $assignment->gradeComponent?->name ?? 'Belum masuk rubrik'
            );
            $assignment->setAttribute(
                'grade_category_weight',
                $assignment->gradeComponent?->weight
            );
        });
        $gradedAssignments = $assignments->filter(
            fn (Assignment $assignment) => $assignment->grade_component_id !== null
                && $assignment->submissions->first()?->grade
        );
        $gradedComponentGroups = $gradedAssignments->groupBy('grade_component_id');
        $gradedWeight = $gradedComponentGroups->sum(
            fn ($group) => (float) $group->first()->gradeComponent->weight
        );
        $totalWeightedScore = $gradedComponentGroups->sum(function ($group) {
            $weight = (float) $group->first()->gradeComponent->weight;
            $componentAverage = $group->avg(function (Assignment $assignment) {
                $submission = $assignment->submissions->first();

                return ((float) $submission->grade->score / max($assignment->max_score, 1)) * 100;
            });

            return ($componentAverage * $weight) / 100;
        });
        $finalScore = $gradedWeight > 0
            ? ($totalWeightedScore / $gradedWeight) * 100
            : null;
        $finalScore = $finalScore !== null
            ? round($finalScore + 1e-9, 2, PHP_ROUND_HALF_UP)
            : null;
        $finalLetterGrade = match (true) {
            $finalScore === null => null,
            $finalScore >= 86 => 'A',
            $finalScore >= 76 => 'AB',
            $finalScore >= 66 => 'B',
            $finalScore >= 56 => 'BC',
            $finalScore >= 46 => 'C',
            $finalScore >= 36 => 'D',
            default => 'E',
        };
        $totalRubricWeight = (float) $gradeComponents->sum('weight');

        return view('mahasiswa.grades.show', compact(
            'course',
            'assignments',
            'gradeComponents',
            'totalRubricWeight',
            'finalScore',
            'finalLetterGrade'
        ));
    }

    public function createSubmission(Course $course, Assignment $assignment)
    {
        Gate::authorize('create', [Submission::class, $assignment]);

        return view('mahasiswa.submissions.create', compact(
            'course',
            'assignment'
        ));
    }
}
