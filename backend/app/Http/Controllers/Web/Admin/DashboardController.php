<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Child;
use App\Models\Enrollment;
use App\Models\Notification;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Stat cards ────────────────────────────────────────────────
        $totalChildren = Child::count();
        $activeEnrollments = Enrollment::where('status', 'approved')->count();
        $totalReports = Report::count();

        $today = Carbon::today()->toDateString();
        $presentToday = Attendance::where('attendance_date', $today)->where('status', 'present')->count();
        $absentToday = Attendance::where('attendance_date', $today)->where('status', 'absent')->count();
        $lateToday = Attendance::where('attendance_date', $today)->where('status', 'late')->count();
        $excusedToday = Attendance::where('attendance_date', $today)->where('status', 'excused')->count();

        // ── Attendance chart — per-day present count this month ───────
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        $daysInMonth = Carbon::now()->daysInMonth;

        $presMap = Attendance::whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->where('status', 'present')
            ->selectRaw('DAY(attendance_date) as d, count(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd');

        $chartLabels = [];
        $chartData = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $chartLabels[] = Carbon::now()->startOfMonth()->addDays($d - 1)->format('M j');
            $chartData[] = $presMap[$d] ?? 0;
        }

        // Monthly attendance legend totals
        $presMonth = Attendance::whereYear('attendance_date', $year)->whereMonth('attendance_date', $month)->where('status', 'present')->count();
        $absMonth = Attendance::whereYear('attendance_date', $year)->whereMonth('attendance_date', $month)->where('status', 'absent')->count();
        $lateMonth = Attendance::whereYear('attendance_date', $year)->whereMonth('attendance_date', $month)->where('status', 'late')->count();
        $excusedMonth = Attendance::whereYear('attendance_date', $year)->whereMonth('attendance_date', $month)->where('status', 'excused')->count();

        // ── Recent activities ─────────────────────────────────────────
        $recentActivities = collect();

        $recentEnrolls = Enrollment::with('child')->orderBy('created_at', 'desc')->take(2)->get();
        foreach ($recentEnrolls as $e) {
            $recentActivities->push([
                'icon' => 'fas fa-user-plus',
                'ibg' => '#e8f5e9',
                'ic' => '#2e7d32',
                'title' => 'New enrollment',
                'sub' => ($e->child->first_name ?? 'Child') . ' ' . ($e->child->last_name ?? '') . ' has been enrolled.',
                'time' => $e->created_at->diffForHumans(null, true, true),
            ]);
        }

        $attCount = Attendance::whereDate('attendance_date', $today)->count();
        if ($attCount > 0) {
            $recentActivities->push([
                'icon' => 'fas fa-calendar-check',
                'ibg' => '#fff3e0',
                'ic' => '#e65100',
                'title' => 'Attendance recorded',
                'sub' => $presentToday . ' children marked present today.',
                'time' => 'Today',
            ]);
        }

        $latestReport = Report::latest()->first();
        if ($latestReport) {
            $recentActivities->push([
                'icon' => 'fas fa-file-alt',
                'ibg' => '#e3f2fd',
                'ic' => '#1565c0',
                'title' => 'Report generated',
                'sub' => $latestReport->title,
                'time' => $latestReport->generated_at->diffForHumans(null, true, true),
            ]);
        }

        $latestNotif = Notification::latest()->first();
        if ($latestNotif) {
            $recentActivities->push([
                'icon' => 'fas fa-bell',
                'ibg' => '#f3e5f5',
                'ic' => '#6a1b9a',
                'title' => 'New notification sent',
                'sub' => \Str::limit($latestNotif->message, 60),
                'time' => $latestNotif->created_at->diffForHumans(null, true, true),
            ]);
        }

        $latestUser = User::latest()->first();
        if ($latestUser) {
            $recentActivities->push([
                'icon' => 'fas fa-user',
                'ibg' => '#e8f5e9',
                'ic' => '#388e3c',
                'title' => 'New user added',
                'sub' => ucfirst($latestUser->role) . ' "' . $latestUser->name . '" has been added.',
                'time' => $latestUser->created_at->diffForHumans(null, true, true),
            ]);
        }

        // ── Enrollment donut ──────────────────────────────────────────
        $enActive = Enrollment::where('status', 'approved')->count();
        $enPending = Enrollment::where('status', 'pending')->count();
        $enRejected = Enrollment::where('status', 'rejected')->count();
        $enTotal = $enActive + $enPending + $enRejected;

        $enActivePct = $enTotal ? round($enActive / $enTotal * 100, 1) : 0;
        $enPendingPct = $enTotal ? round($enPending / $enTotal * 100, 1) : 0;
        $enRejPct = $enTotal ? round($enRejected / $enTotal * 100, 1) : 0;

        return view('admin.dashboard', compact(
            'totalChildren',
            'activeEnrollments',
            'presentToday',
            'totalReports',
            'absentToday',
            'lateToday',
            'excusedToday',
            'presMonth',
            'absMonth',
            'lateMonth',
            'excusedMonth',
            'chartLabels',
            'chartData',
            'recentActivities',
            'enActive',
            'enPending',
            'enRejected',
            'enTotal',
            'enActivePct',
            'enPendingPct',
            'enRejPct'
        ));
    }
}
