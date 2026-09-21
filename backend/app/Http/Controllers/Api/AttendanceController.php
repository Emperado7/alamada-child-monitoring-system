<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Child;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    /**
     * GET /api/attendance
     * Filters: child_id, date, month (YYYY-MM), status, search
     */
    public function index(Request $request): JsonResponse
    {
        $user  = $request->user();
        $query = Attendance::with(['child', 'recorder:id,name'])
            ->join('children', 'attendances.child_id', '=', 'children.id')
            ->select('attendances.*');

        if ($user->isParent()) {
            $query->where('children.parent_id', $user->id);
        } elseif ($user->isStaff()) {
            $query->where('children.staff_id', $user->id);
        }

        if ($request->filled('child_id')) {
            $query->where('attendances.child_id', $request->child_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('attendances.attendance_date', $request->date);
        }
        if ($request->filled('month')) {
            // Expects YYYY-MM
            $query->whereYear('attendances.attendance_date', substr($request->month, 0, 4))
                  ->whereMonth('attendances.attendance_date', substr($request->month, 5, 2));
        }
        if ($request->filled('status')) {
            $query->where('attendances.status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('children.first_name', 'like', "%{$request->search}%")
                  ->orWhere('children.last_name', 'like', "%{$request->search}%");
            });
        }

        $records = $query->orderBy('attendances.attendance_date', 'desc')->paginate(20);

        return response()->json($records);
    }

    /**
     * POST /api/attendance
     * Staff records attendance for one child.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'child_id'        => 'required|exists:children,id',
            'attendance_date' => 'required|date',
            'status'          => 'required|in:present,absent,late,excused',
            'remarks'         => 'nullable|string',
        ]);

        $data['recorded_by'] = $request->user()->id;

        $attendance = Attendance::updateOrCreate(
            ['child_id' => $data['child_id'], 'attendance_date' => $data['attendance_date']],
            $data
        );

        return response()->json([
            'message'    => 'Attendance recorded successfully.',
            'attendance' => $attendance->load('child'),
        ], 201);
    }

    /**
     * POST /api/attendance/bulk
     * Staff records attendance for multiple children at once.
     * Body: { date: "YYYY-MM-DD", records: [{ child_id, status, remarks? }] }
     */
    public function bulkStore(Request $request): JsonResponse
    {
        $request->validate([
            'date'              => 'required|date',
            'records'           => 'required|array|min:1',
            'records.*.child_id' => 'required|exists:children,id',
            'records.*.status'   => 'required|in:present,absent,late,excused',
            'records.*.remarks'  => 'nullable|string',
        ]);

        $date      = $request->date;
        $staffId   = $request->user()->id;
        $saved     = [];

        foreach ($request->records as $record) {
            $saved[] = Attendance::updateOrCreate(
                ['child_id' => $record['child_id'], 'attendance_date' => $date],
                [
                    'status'      => $record['status'],
                    'remarks'     => $record['remarks'] ?? null,
                    'recorded_by' => $staffId,
                ]
            );
        }

        return response()->json([
            'message' => count($saved) . ' attendance record(s) saved.',
            'records' => $saved,
        ]);
    }

    /**
     * GET /api/attendance/{id}
     */
    public function show(Attendance $attendance): JsonResponse
    {
        return response()->json($attendance->load(['child', 'recorder:id,name']));
    }

    /**
     * PUT /api/attendance/{id}
     */
    public function update(Request $request, Attendance $attendance): JsonResponse
    {
        $data = $request->validate([
            'status'  => 'sometimes|in:present,absent,late,excused',
            'remarks' => 'nullable|string',
        ]);

        $attendance->update($data);

        return response()->json([
            'message'    => 'Attendance updated successfully.',
            'attendance' => $attendance->fresh()->load(['child', 'recorder:id,name']),
        ]);
    }

    /**
     * DELETE /api/attendance/{id}  (Admin only)
     */
    public function destroy(Attendance $attendance): JsonResponse
    {
        $attendance->delete();
        return response()->json(['message' => 'Attendance record deleted.']);
    }

    /**
     * GET /api/attendance/summary
     * Summary stats: present/absent/late counts per child for a given month.
     */
    public function summary(Request $request): JsonResponse
    {
        $request->validate(['month' => 'required|date_format:Y-m']);

        [$year, $month] = explode('-', $request->month);

        $user  = $request->user();
        $query = Attendance::with('child:id,first_name,last_name')
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->join('children', 'attendances.child_id', '=', 'children.id')
            ->select('attendances.*');

        if ($user->isParent()) {
            $query->where('children.parent_id', $user->id);
        } elseif ($user->isStaff()) {
            $query->where('children.staff_id', $user->id);
        }

        $records = $query->get();

        $summary = $records->groupBy('child_id')->map(function ($group) {
            $child = $group->first()->child;
            return [
                'child_id'   => $child->id,
                'child_name' => $child->full_name,
                'present'    => $group->where('status', 'present')->count(),
                'absent'     => $group->where('status', 'absent')->count(),
                'late'       => $group->where('status', 'late')->count(),
                'excused'    => $group->where('status', 'excused')->count(),
                'total_days' => $group->count(),
            ];
        })->values();

        return response()->json(['month' => $request->month, 'summary' => $summary]);
    }
}
