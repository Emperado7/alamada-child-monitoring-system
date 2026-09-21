<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Child;
use App\Models\Enrollment;
use App\Models\Notification;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $staffId = auth()->id();
        $today   = Carbon::today()->toDateString();

        // My children stats
        $myChildren         = Child::where('staff_id', $staffId)->count();
        $activeEnrollments  = Enrollment::where('status', 'approved')
            ->whereHas('child', fn($q) => $q->where('staff_id', $staffId))->count();
        $pendingEnrollments = Enrollment::where('status', 'pending')
            ->whereHas('child', fn($q) => $q->where('staff_id', $staffId))->count();

        // Today's attendance
        $presentToday = Attendance::where('attendance_date', $today)
            ->where('status', 'present')
            ->whereHas('child', fn($q) => $q->where('staff_id', $staffId))->count();

        $absentToday = Attendance::where('attendance_date', $today)
            ->where('status', 'absent')
            ->whereHas('child', fn($q) => $q->where('staff_id', $staffId))->count();

        // Monthly attendance rate
        $monthRecords = Attendance::whereYear('attendance_date', Carbon::now()->year)
            ->whereMonth('attendance_date', Carbon::now()->month)
            ->whereHas('child', fn($q) => $q->where('staff_id', $staffId))
            ->get();
        $monthRate = $monthRecords->count()
            ? round($monthRecords->where('status', 'present')->count()
                    / $monthRecords->count() * 100, 1)
            : 0;

        // My active children for quick list
        $children = Child::where('staff_id', $staffId)
            ->where('status', 'active')
            ->orderBy('last_name')
            ->take(8)->get();

        // Notifications for staff
        $notifications = Notification::where(function ($q) {
            $q->whereIn('recipient_group', ['all', 'staff'])
              ->orWhere('recipient_id', auth()->id());
        })->orderBy('created_at', 'desc')->take(5)->get();

        return view('staff.dashboard', compact(
            'myChildren', 'activeEnrollments', 'pendingEnrollments',
            'presentToday', 'absentToday', 'monthRate',
            'children', 'notifications'
        ));
    }
}
