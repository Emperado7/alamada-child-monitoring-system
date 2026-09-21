<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Admin;
use App\Http\Controllers\Web\Staff;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Alamada LGU Child Monitoring System
|--------------------------------------------------------------------------
*/

// ── Root redirect ────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ── Auth ──────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get ('/login',  [LoginController::class, 'showForm'])->name('login');
    Route::post('/login',  [LoginController::class, 'login'])->name('login.post');
});
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Admin ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Children
    Route::resource('children', Admin\ChildController::class);

    // Enrollment
    Route::get   ('/enrollment',             [Admin\EnrollmentController::class, 'index'])->name('enrollment.index');
    Route::get   ('/enrollment/create',      [Admin\EnrollmentController::class, 'create'])->name('enrollment.create');
    Route::post  ('/enrollment',             [Admin\EnrollmentController::class, 'store'])->name('enrollment.store');
    Route::get   ('/enrollment/{enrollment}', [Admin\EnrollmentController::class, 'show'])->name('enrollment.show');
    Route::post  ('/enrollment/{enrollment}/approve', [Admin\EnrollmentController::class, 'approve'])->name('enrollment.approve');
    Route::post  ('/enrollment/{enrollment}/reject',  [Admin\EnrollmentController::class, 'reject'])->name('enrollment.reject');
    Route::delete('/enrollment/{enrollment}',         [Admin\EnrollmentController::class, 'destroy'])->name('enrollment.destroy');

    // Attendance
    Route::get ('/attendance',                [Admin\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get ('/attendance/mark',           [Admin\AttendanceController::class, 'markForm'])->name('attendance.mark');
    Route::post('/attendance/mark',           [Admin\AttendanceController::class, 'markSave'])->name('attendance.save');
    Route::get ('/attendance/{child}',        [Admin\AttendanceController::class, 'show'])->name('attendance.show');
    Route::delete('/attendance/{attendance}', [Admin\AttendanceController::class, 'destroy'])->name('attendance.destroy');

    // Reports
    Route::get ('/reports',               [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get ('/reports/enrollment',    [Admin\ReportController::class, 'enrollment'])->name('reports.enrollment');
    Route::get ('/reports/attendance',    [Admin\ReportController::class, 'attendance'])->name('reports.attendance');
    Route::get ('/reports/{report}',      [Admin\ReportController::class, 'show'])->name('reports.show');
    Route::delete('/reports/{report}',    [Admin\ReportController::class, 'destroy'])->name('reports.destroy');

    // Notifications
    Route::get  ('/notifications',         [Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::get  ('/notifications/create',  [Admin\NotificationController::class, 'create'])->name('notifications.create');
    Route::post ('/notifications',         [Admin\NotificationController::class, 'store'])->name('notifications.store');
    Route::delete('/notifications/{notification}', [Admin\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Users
    Route::resource('users', Admin\UserController::class)->except(['show']);

    // Settings
    Route::get ('/settings',  [Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings',  [Admin\SettingsController::class, 'update'])->name('settings.update');
});

// ── Staff ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {

    Route::get('/dashboard', [Staff\DashboardController::class, 'index'])->name('dashboard');

    // Children (view + detail)
    Route::get('/children',       [Staff\ChildController::class, 'index'])->name('children.index');
    Route::get('/children/{child}', [Staff\ChildController::class, 'show'])->name('children.show');

    // Enrollment (view only)
    Route::get('/enrollment',               [Staff\EnrollmentController::class, 'index'])->name('enrollment.index');
    Route::get('/enrollment/create',        [Staff\EnrollmentController::class, 'create'])->name('enrollment.create');
    Route::post('/enrollment',              [Staff\EnrollmentController::class, 'store'])->name('enrollment.store');
    Route::get('/enrollment/{enrollment}',  [Staff\EnrollmentController::class, 'show'])->name('enrollment.show');

    // Attendance
    Route::get  ('/attendance',             [Staff\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get  ('/attendance/mark',        [Staff\AttendanceController::class, 'markForm'])->name('attendance.mark');
    Route::post ('/attendance/mark',        [Staff\AttendanceController::class, 'markSave'])->name('attendance.save');

    // Activities
    Route::get  ('/activities',             [Staff\ActivityController::class, 'index'])->name('activities.index');
    Route::get  ('/activities/create',      [Staff\ActivityController::class, 'create'])->name('activities.create');
    Route::post ('/activities',             [Staff\ActivityController::class, 'store'])->name('activities.store');
    Route::get  ('/activities/{activity}/edit', [Staff\ActivityController::class, 'edit'])->name('activities.edit');
    Route::put  ('/activities/{activity}',  [Staff\ActivityController::class, 'update'])->name('activities.update');
    Route::delete('/activities/{activity}', [Staff\ActivityController::class, 'destroy'])->name('activities.destroy');

    // Notifications (view only)
    Route::get('/notifications', [Staff\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [Staff\NotificationController::class, 'markRead'])->name('notifications.read');

    // Settings
    Route::get ('/settings',       [Staff\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings',       [Staff\SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/password', [Staff\SettingsController::class, 'changePassword'])->name('settings.password');
});
