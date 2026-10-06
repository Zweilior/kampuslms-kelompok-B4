<?php

use App\Http\Controllers\Api\V1\AssignmentController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\NotificationController;
use Illuminate\Support\Facades\Route;

Route::post('/v1/auth/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');

Route::prefix('v1')
    ->middleware(['auth:sanctum', 'throttle:60,1'])
    ->group(function () {
        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Courses
        Route::get('/courses', [CourseController::class, 'index']);
        Route::get('/courses/{course}', [CourseController::class, 'show']);
        Route::get('/courses/{course}/materials', [CourseController::class, 'materials']);
        Route::get('/courses/{course}/assignments', [CourseController::class, 'assignments']);

        // Assignments
        Route::post('/assignments', [AssignmentController::class, 'store']);
        Route::match(['put', 'patch'], '/assignments/{assignment}', [AssignmentController::class, 'update']);
        Route::delete('/assignments/{assignment}', [AssignmentController::class, 'destroy']);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    });