<?php

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChildController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Alamada LGU Child Monitoring System
|--------------------------------------------------------------------------
| Base URL: /api
| Auth: Laravel Sanctum (token-based)
|
| Roles:
|   admin  → full access
|   staff  → manage children, attendance, activities, own enrollment
|   parent → read-only access to their own child's data
|--------------------------------------------------------------------------
*/

// ── Public ────────────────────────────────────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);

// ── Authenticated ─────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout',           [AuthController::class, 'logout']);
    Route::get('/me',                [AuthController::class, 'me']);
    Route::put('/me/password',       [AuthController::class, 'changePassword']);

    // ── Admin only ────────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // User management
        Route::get('/users',                       [UserController::class, 'index']);
        Route::post('/users',                      [UserController::class, 'store']);
        Route::get('/users/{user}',                [UserController::class, 'show']);
        Route::put('/users/{user}',                [UserController::class, 'update']);
        Route::delete('/users/{user}',             [UserController::class, 'destroy']);
        Route::put('/users/{user}/reset-password', [UserController::class, 'resetPassword']);

        // Enrollment approval
        Route::put('/enrollments/{enrollment}/approve', [EnrollmentController::class, 'approve']);
        Route::put('/enrollments/{enrollment}/reject',  [EnrollmentController::class, 'reject']);
        Route::delete('/enrollments/{enrollment}',      [EnrollmentController::class, 'destroy']);

        // Reports (admin generates)
        Route::get('/reports',                     [ReportController::class, 'index']);
        Route::get('/reports/enrollment',          [ReportController::class, 'enrollmentReport']);
        Route::get('/reports/attendance',          [ReportController::class, 'attendanceReport']);
        Route::get('/reports/{report}',            [ReportController::class, 'show']);
        Route::delete('/reports/{report}',         [ReportController::class, 'destroy']);

        // Notifications (send & manage)
        Route::post('/notifications',              [NotificationController::class, 'store']);
        Route::put('/notifications/{notification}', [NotificationController::class, 'update']);
        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']);
    });

    // ── Admin + Staff ─────────────────────────────────────────────────────
    Route::middleware('role:admin,staff')->group(function () {
        // Children — full CRUD for admin/staff
        Route::get('/children',                        [ChildController::class, 'index']);
        Route::post('/children',                       [ChildController::class, 'store']);
        Route::get('/children/{child}',                [ChildController::class, 'show']);
        Route::post('/children/{child}',               [ChildController::class, 'update']); // POST for multipart/form-data
        Route::delete('/children/{child}',             [ChildController::class, 'destroy']);
        Route::get('/children/{child}/activities',     [ChildController::class, 'activities']);

        // Enrollment
        Route::get('/enrollments',             [EnrollmentController::class, 'index']);
        Route::post('/enrollments',            [EnrollmentController::class, 'store']);
        Route::get('/enrollments/{enrollment}', [EnrollmentController::class, 'show']);
        Route::put('/enrollments/{enrollment}', [EnrollmentController::class, 'update']);

        // Attendance
        Route::get('/attendance',              [AttendanceController::class, 'index']);
        Route::post('/attendance',             [AttendanceController::class, 'store']);
        Route::post('/attendance/bulk',        [AttendanceController::class, 'bulkStore']);
        Route::get('/attendance/summary',      [AttendanceController::class, 'summary']);
        Route::get('/attendance/{attendance}', [AttendanceController::class, 'show']);
        Route::put('/attendance/{attendance}', [AttendanceController::class, 'update']);
        Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy']);

        // Activities
        Route::apiResource('/activities', ActivityController::class);
    });

    // ── Parent (read-only via mobile) ─────────────────────────────────────
    Route::middleware('role:parent')->prefix('parent')->group(function () {
        // Child info
        Route::get('/children',                    [ChildController::class, 'index']);
        Route::get('/children/{child}',            [ChildController::class, 'show']);
        Route::get('/children/{child}/activities', [ChildController::class, 'activities']);

        // Attendance (view only)
        Route::get('/attendance',              [AttendanceController::class, 'index']);
        Route::get('/attendance/summary',      [AttendanceController::class, 'summary']);
        Route::get('/attendance/{attendance}', [AttendanceController::class, 'show']);

        // Enrollment (view only)
        Route::get('/enrollments',              [EnrollmentController::class, 'index']);
        Route::get('/enrollments/{enrollment}', [EnrollmentController::class, 'show']);
    });

    // ── Notifications (all authenticated users) ───────────────────────────
    Route::get('/notifications',                       [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count',          [NotificationController::class, 'unreadCount']);
    Route::get('/notifications/{notification}',        [NotificationController::class, 'show']);
    Route::post('/notifications/{notification}/read',  [NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all',             [NotificationController::class, 'markAllRead']);
});
