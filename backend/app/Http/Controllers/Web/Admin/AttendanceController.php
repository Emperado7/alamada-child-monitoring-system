<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date  = $request->input('date',  Carbon::today()->toDateString());
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        [$yr, $mo] = explode('-', $month);

        // Build children query with today's attendance eager-loaded
        $query = Child::where('status', 'active')
            ->with(['attendances' => fn($q) =>
                $q->where('attendance_date', $date)
            ]);

        // Search filter
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('first_name','like',"%$s%")
                  ->orWhere('last_name', 'like',"%$s%")
            );
        }

        // Status filter — only show children with that attendance status
        if ($request->filled('status')) {
            $st = $request->status;
            $query->whereHas('attendances', fn($q) =>
                $q->where('attendance_date', $date)
                  ->where('status', $st)
            );
        }

        $children = $query->orderBy('last_name')->paginate(10)->withQueryString();

        // Summary stats passed separately
        $summary = [
            'present' => Attendance::where('attendance_date',$date)->where('status','present')->count(),
            'absent'  => Attendance::where('attendance_date',$date)->where('status','absent')->count(),
            'late'    => Attendance::where('attendance_date',$date)->where('status','late')->count(),
            'excused' => Attendance::where('attendance_date',$date)->where('status','excused')->count(),
        ];

        // Monthly rate
        $monthRecords = Attendance::whereYear('attendance_date',$yr)
            ->whereMonth('attendance_date',$mo)->get();
        $monthRate = $monthRecords->count()
            ? round($monthRecords->where('status','present')->count()
                    / $monthRecords->count() * 100, 1)
            : 0;

        return view('admin.attendance.index', compact(
            'children', 'date', 'month', 'summary', 'monthRate'
        ));
    }

    public function show(Request $request, Child $child)
    {
        $month     = $request->input('month', Carbon::now()->format('Y-m'));
        [$yr, $mo] = explode('-', $month);

        $records = Attendance::where('child_id', $child->id)
            ->whereYear('attendance_date',  $yr)
            ->whereMonth('attendance_date', $mo)
            ->with('recorder:id,name')
            ->orderBy('attendance_date')
            ->get();

        $summary = [
            'present' => $records->where('status','present')->count(),
            'absent'  => $records->where('status','absent')->count(),
            'late'    => $records->where('status','late')->count(),
            'excused' => $records->where('status','excused')->count(),
            'total'   => $records->count(),
        ];
        $summary['rate'] = $summary['total']
            ? round($summary['present'] / $summary['total'] * 100, 1)
            : 0;

        return view('admin.attendance.show',
            compact('child','records','summary','month'));
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return back()->with('success','Attendance record deleted.');
    }
}
