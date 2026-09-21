<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Child;
use App\Models\Enrollment;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalChildren      = Child::count();
        $activeEnrollments  = Enrollment::where('status', 'approved')->count();
        $pendingEnrollments = Enrollment::where('status', 'pending')->count();
        $totalStaff         = User::where('role', 'staff')->where('status', 'active')->count();
        $totalParents       = User::where('role', 'parent')->count();

        // Today's attendance
        $today         = Carbon::today()->toDateString();
        $presentToday  = Attendance::where('attendance_date', $today)->where('status', 'present')->count();
        $absentToday   = Attendance::where('attendance_date', $today)->where('status', 'absent')->count();

        // Attendance rate for current month
        $month        = Carbon::now()->format('Y-m');
        $monthRecords = Attendance::whereYear('attendance_date', Carbon::now()->year)
            ->whereMonth('attendance_date', Carbon::now()->month)->get();
        $attendanceRate = $monthRecords->count()
            ? round($monthRecords->where('status', 'present')->count() / $monthRecords->count() * 100, 1)
            : 0;

        // Recent enrollments
        $recentEnrollments = Enrollment::with('child')
            ->orderBy('created_at', 'desc')->take(5)->get();

        // Enrollment trend: current year vs previous year
        $currentYear    = Carbon::now()->year;
        $previousYear   = $currentYear - 1;
        $currentYearCount  = Enrollment::where('status', 'approved')->where('school_year', $currentYear)->count();
        $previousYearCount = Enrollment::where('status', 'approved')->where('school_year', $previousYear)->count();

        // By diagnosis
        $byDiagnosis = Child::selectRaw('diagnosis, count(*) as total')
            ->groupBy('diagnosis')->pluck('total', 'diagnosis');

        // By gender
        $byGender = Child::selectRaw('gender, count(*) as total')
            ->groupBy('gender')->pluck('total', 'gender');

        // Recent notifications
        $recentNotifications = Notification::orderBy('created_at', 'desc')->take(4)->get();

        return view('admin.dashboard', compact(
            'totalChildren', 'activeEnrollments', 'pendingEnrollments',
            'totalStaff', 'totalParents',
            'presentToday', 'absentToday', 'attendanceRate',
            'recentEnrollments', 'currentYearCount', 'previousYearCount',
            'currentYear', 'previousYear',
            'byDiagnosis', 'byGender', 'recentNotifications'
        ));
    }
}
