<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Child;
use App\Models\Enrollment;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with('generatedBy:id,name');

        if ($request->filled('report_type'))  $query->where('report_type', $request->report_type);
        if ($request->filled('generated_by')) $query->where('generated_by', $request->generated_by);
        if ($request->filled('status'))       $query->where('status', $request->status);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('title','like',"%$s%");
        }
        if ($request->filled('date_from'))
            $query->whereDate('generated_at','>=',$request->date_from);
        if ($request->filled('date_to'))
            $query->whereDate('generated_at','<=',$request->date_to);

        $reports = $query->orderBy('generated_at','desc')->paginate(10)->withQueryString();

        return view('admin.reports.index', compact('reports'));
    }

    public function destroy(Report $report): \Illuminate\Http\RedirectResponse
    {
        $report->delete();
        return redirect()->route('admin.reports.index')->with('success','Report deleted.');
    }

    public function enrollment(Request $request)
    {
        $currentYear  = Carbon::now()->year;
        $previousYear = $currentYear - 1;

        $query = Enrollment::where('enrollments.status','approved')
            ->join('children','enrollments.child_id','=','children.id')
            ->select('children.*','enrollments.school_year','enrollments.enrollment_date','enrollments.id as enrollment_id');

        if ($request->filled('gender'))      $query->where('children.gender', $request->gender);
        if ($request->filled('school_year')) $query->where('enrollments.school_year', $request->school_year);
        if ($request->filled('age_min'))     $query->whereRaw('TIMESTAMPDIFF(YEAR,children.date_of_birth,CURDATE()) >= ?', [$request->age_min]);
        if ($request->filled('age_max'))     $query->whereRaw('TIMESTAMPDIFF(YEAR,children.date_of_birth,CURDATE()) <= ?', [$request->age_max]);

        $data = $query->get();

        $stats = [
            'total'        => $data->count(),
            'male'         => $data->where('gender','male')->count(),
            'female'       => $data->where('gender','female')->count(),
            'current_year' => Enrollment::where('status','approved')->where('school_year',$currentYear)->count(),
            'prev_year'    => Enrollment::where('status','approved')->where('school_year',$previousYear)->count(),
        ];

        $byDiagnosis = $data->groupBy('diagnosis')->map->count();

        // Save report log
        Report::create([
            'report_type'  => 'enrollment',
            'title'        => 'Enrollment Report — '.Carbon::now()->format('F Y'),
            'filters'      => $request->only(['gender','school_year','age_min','age_max']),
            'format'       => 'web',
            'status'       => 'generated',
            'generated_by' => auth()->id(),
            'generated_at' => Carbon::now(),
        ]);

        $years = Enrollment::selectRaw('DISTINCT school_year')->orderBy('school_year','desc')->pluck('school_year');
        return view('admin.reports.enrollment', compact('data','stats','byDiagnosis','currentYear','previousYear','years'));
    }

    public function attendance(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        [$yr, $mo] = explode('-', $month);

        $records = Attendance::with('child:id,first_name,last_name,gender,diagnosis')
            ->whereYear('attendance_date',$yr)->whereMonth('attendance_date',$mo)
            ->join('children','attendances.child_id','=','children.id')
            ->select('attendances.*')
            ->get();

        $summary = $records->groupBy('child_id')->map(function($group) {
            $child = $group->first()->child;
            $total = $group->count();
            $present = $group->where('status','present')->count();
            return [
                'child'   => $child,
                'present' => $present,
                'absent'  => $group->where('status','absent')->count(),
                'late'    => $group->where('status','late')->count(),
                'excused' => $group->where('status','excused')->count(),
                'total'   => $total,
                'rate'    => $total ? round($present/$total*100,1) : 0,
            ];
        })->values();

        return view('admin.reports.attendance', compact('summary','month'));
    }
}
