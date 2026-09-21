<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Child;
use App\Models\Enrollment;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    /**
     * GET /api/reports
     */
    public function index(Request $request): JsonResponse
    {
        $reports = Report::with('generatedBy:id,name')
            ->when($request->filled('report_type'), fn ($q) => $q->where('report_type', $request->report_type))
            ->orderBy('generated_at', 'desc')
            ->paginate(15);

        return response()->json($reports);
    }

    /**
     * GET /api/reports/enrollment
     * Total enrolled students with filters: age, gender, year (current/previous)
     */
    public function enrollmentReport(Request $request): JsonResponse
    {
        $request->validate([
            'gender'      => 'nullable|in:male,female',
            'school_year' => 'nullable|digits:4|integer',
            'age_min'     => 'nullable|integer|min:5|max:16',
            'age_max'     => 'nullable|integer|min:5|max:16',
        ]);

        $currentYear  = Carbon::now()->year;
        $previousYear = $currentYear - 1;

        // Base query: approved enrollments joined with children
        $base = Enrollment::where('enrollments.status', 'approved')
            ->join('children', 'enrollments.child_id', '=', 'children.id')
            ->select('children.*', 'enrollments.school_year', 'enrollments.enrollment_date');

        // Apply filters
        if ($request->filled('gender')) {
            $base->where('children.gender', $request->gender);
        }
        if ($request->filled('school_year')) {
            $base->where('enrollments.school_year', $request->school_year);
        }
        if ($request->filled('age_min') || $request->filled('age_max')) {
            $base->whereBetween(
                \DB::raw('TIMESTAMPDIFF(YEAR, children.date_of_birth, CURDATE())'),
                [$request->age_min ?? 0, $request->age_max ?? 100]
            );
        }

        $all = $base->get();

        // Current vs previous year counts (using all approved, no age/gender filter on these)
        $currentCount  = Enrollment::where('status', 'approved')
            ->where('school_year', $currentYear)->count();
        $previousCount = Enrollment::where('status', 'approved')
            ->where('school_year', $previousYear)->count();

        $byGender    = $all->groupBy('gender')->map->count();
        $byDiagnosis = $all->groupBy('diagnosis')->map->count();
        $byYear      = $all->groupBy('school_year')->map->count();

        // Age distribution
        $ageGroups = $all->groupBy(function ($child) {
            $age = Carbon::parse($child->date_of_birth)->age;
            if ($age <= 7)  return '5-7';
            if ($age <= 10) return '8-10';
            if ($age <= 13) return '11-13';
            return '14-16';
        })->map->count();

        // Save report record
        $report = Report::create([
            'report_type'  => 'enrollment',
            'title'        => 'Enrollment Report — ' . Carbon::now()->format('F Y'),
            'filters'      => $request->only(['gender', 'school_year', 'age_min', 'age_max']),
            'format'       => 'json',
            'status'       => 'generated',
            'generated_by' => $request->user()->id,
            'generated_at' => Carbon::now(),
        ]);

        return response()->json([
            'report_id'        => $report->id,
            'generated_at'     => $report->generated_at,
            'filters_applied'  => $request->only(['gender', 'school_year', 'age_min', 'age_max']),
            'total_enrolled'   => $all->count(),
            'current_year'     => ['year' => $currentYear, 'count' => $currentCount],
            'previous_year'    => ['year' => $previousYear, 'count' => $previousCount],
            'by_gender'        => $byGender,
            'by_diagnosis'     => $byDiagnosis,
            'by_school_year'   => $byYear,
            'by_age_group'     => $ageGroups,
            'children'         => $all->map(fn ($c) => [
                'id'            => $c->id,
                'name'          => trim("{$c->first_name} {$c->middle_name} {$c->last_name}"),
                'age'           => Carbon::parse($c->date_of_birth)->age,
                'gender'        => $c->gender,
                'diagnosis'     => $c->diagnosis,
                'school_year'   => $c->school_year,
                'enrolled_date' => $c->enrollment_date,
            ]),
        ]);
    }

    /**
     * GET /api/reports/attendance
     * Attendance summary: filters by month, child_id, status
     */
    public function attendanceReport(Request $request): JsonResponse
    {
        $request->validate([
            'month'    => 'required|date_format:Y-m',
            'child_id' => 'nullable|exists:children,id',
        ]);

        [$year, $month] = explode('-', $request->month);

        $query = Attendance::with('child:id,first_name,last_name,gender,diagnosis')
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->join('children', 'attendances.child_id', '=', 'children.id')
            ->select('attendances.*');

        if ($request->filled('child_id')) {
            $query->where('attendances.child_id', $request->child_id);
        }

        $records = $query->orderBy('attendances.attendance_date')->get();

        $summary = $records->groupBy('child_id')->map(function ($group) {
            $child = $group->first()->child;
            $total = $group->count();
            $present = $group->where('status', 'present')->count();
            return [
                'child_id'        => $child->id,
                'child_name'      => trim("{$child->first_name} {$child->last_name}"),
                'gender'          => $child->gender,
                'diagnosis'       => $child->diagnosis,
                'present'         => $present,
                'absent'          => $group->where('status', 'absent')->count(),
                'late'            => $group->where('status', 'late')->count(),
                'excused'         => $group->where('status', 'excused')->count(),
                'total_days'      => $total,
                'attendance_rate' => $total > 0 ? round(($present / $total) * 100, 1) . '%' : '0%',
            ];
        })->values();

        return response()->json([
            'month'           => $request->month,
            'total_children'  => $summary->count(),
            'avg_attendance'  => $summary->avg(fn ($s) => (float) rtrim($s['attendance_rate'], '%')) . '%',
            'summary'         => $summary,
        ]);
    }

    /**
     * GET /api/reports/{id}
     */
    public function show(Report $report): JsonResponse
    {
        return response()->json($report->load('generatedBy:id,name'));
    }

    /**
     * DELETE /api/reports/{id}
     */
    public function destroy(Report $report): JsonResponse
    {
        $report->delete();
        return response()->json(['message' => 'Report deleted.']);
    }
}
