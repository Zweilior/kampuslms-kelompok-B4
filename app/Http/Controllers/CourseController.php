<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = [
            [
                'id' => 1,
                'code' => 'SI101',
                'name' => 'Pemrograman Web',
                'lecturer' => 'Budi Santoso',
                'semester' => 3,
            ],
            [
                'id' => 2,
                'code' => 'SI102',
                'name' => 'Basis Data',
                'lecturer' => 'Siti Rahma',
                'semester' => 3,
            ],
            [
                'id' => 3,
                'code' => 'SI103',
                'name' => 'Rekayasa Perangkat Lunak',
                'lecturer' => 'Andi Wijaya',
                'semester' => 3,
            ],
        ];

        return view('courses.index', compact('courses'));
    }

    public function show($course)
    {
        $courses = [
            [
                'id' => 1,
                'code' => 'SI101',
                'name' => 'Pemrograman Web',
                'lecturer' => 'Budi Santoso',
                'semester' => 3,
            ],
            [
                'id' => 2,
                'code' => 'SI102',
                'name' => 'Basis Data',
                'lecturer' => 'Siti Rahma',
                'semester' => 3,
            ],
            [
                'id' => 3,
                'code' => 'SI103',
                'name' => 'Rekayasa Perangkat Lunak',
                'lecturer' => 'Andi Wijaya',
                'semester' => 3,
            ],
        ];

        $selectedCourse = collect($courses)->firstWhere('id', (int) $course);

        abort_if($selectedCourse === null, 404);

        return view('courses.show', [
            'course' => $selectedCourse,
        ]);
    }
}