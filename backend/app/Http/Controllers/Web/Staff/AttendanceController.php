<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    /**
     * Combined index + mark form — matches the screenshot design.
     * The index page IS the attendance marking page.
     */
    public function index(Request $request)
    {
        $staffId      = auth()->id();
        $selectedDate = $request->input('date', Carbon::today()->toDateString());
        $month        = Carbon::parse($selectedDate)->format('Y-m');

        // Build children query
        $query = Child::where('staff_id', $staffId)->where('status', 'active');

        // Search filter
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('first_name', 'like', "%$s%")
                  ->orWhere('last_name',  'like', "%$s%")
                  ->orWhereRaw("CONCAT('CHD-', LPAD(id,3,'0')) LIKE ?", ["%$s%"])
            );
        }

        $children = $query->orderBy('last_name')->get();

        // Load existing attendance for the selected date as a map [child_id => Attendance]
        $childIds      = $children->pluck('id');
        $attendanceMap = Attendance::where('attendance_date', $selectedDate)
            ->whereIn('child_id', $childIds)
            ->get()
            ->keyBy('child_id');

        // If status filter is set, filter children to only those with that status
        if ($request->filled('status')) {
            $st = $request->status;
            $children = $children->filter(function ($child) use ($attendanceMap, $st) {
                $att = $attendanceMap->get($child->id);
                return $att && $att->status === $st;
            })->values();
        }

        return view('staff.attendance.index', compact(
            'children', 'selectedDate', 'month', 'attendanceMap'
        ));
    }

    /**
     * Legacy mark form — redirects to index with date.
     */
    public function markForm(Request $request)
    {
        return redirect()->route('staff.attendance.index', [
            'date' => $request->input('date', Carbon::today()->toDateString()),
        ]);
    }

    /**
     * Save attendance records.
     */
    public function markSave(Request $request)
    {
        $request->validate([
            'date'           => 'required|date',
            'attendance'     => 'required|array',
            'attendance.*'   => 'required|in:present,absent,late,excused',
        ]);

        $date    = $request->date;
        $staffId = auth()->id();

        foreach ($request->attendance as $childId => $status) {
            // Verify the child belongs to this staff member
            $child = Child::find($childId);
            if (!$child || $child->staff_id !== $staffId) continue;

            Attendance::updateOrCreate(
                ['child_id' => $childId, 'attendance_date' => $date],
                [
                    'status'      => $status,
                    'remarks'     => $request->remarks[$childId] ?? null,
                    'recorded_by' => $staffId,
                ]
            );
        }

        return redirect()
            ->route('staff.attendance.index', ['date' => $date])
            ->with('success', 'Attendance saved for '
                . Carbon::parse($date)->format('F d, Y') . '.');
    }
}
