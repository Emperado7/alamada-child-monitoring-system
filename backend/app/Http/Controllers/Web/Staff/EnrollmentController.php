<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Enrollment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50]) ? $perPage : 10;

        // Show all approved enrollments — simple filter, no subquery
        $query = Enrollment::with(['child' => fn($q) => $q->withTrashed(), 'approver:id,name'])
            ->leftJoin('children', 'enrollments.child_id', '=', 'children.id')
            ->select('enrollments.*');

        // Default to approved; use ?status= to show pending/all/etc
        $statusFilter = $request->input('status', 'approved');
        if ($statusFilter !== '') {
            $query->where('enrollments.status', $statusFilter);
        }

        if ($request->filled('school_year'))
            $query->where('enrollments.school_year', $request->school_year);

        if ($request->filled('child_status'))
            $query->where('children.status', $request->child_status);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(
                fn($q) =>
                    $q->where('children.first_name', 'ilike', "%$s%")
                        ->orWhere('children.last_name', 'ilike', "%$s%")
                        ->orWhere('children.guardian_name', 'ilike', "%$s%")
                        ->orWhereRaw("'CHD-' || LPAD(children.id::text, 3, '0') ILIKE ?", ["%$s%"])
            );
        }

        $enrollments = $query->orderBy('enrollments.created_at', 'desc')
            ->paginate($perPage)->withQueryString();

        $years = Enrollment::selectRaw('DISTINCT school_year')
            ->orderBy('school_year', 'desc')->pluck('school_year');

        return view('staff.enrollment.index', compact('enrollments', 'years'));
    }

    public function create()
    {
        return view('staff.enrollment.create');
    }

    public function store(Request $request)
    {
        // Validate child information + enrollment details together
        $data = $request->validate([
            // ── Child fields ──────────────────────────────────────
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'address' => 'required|string|max:255',
            'guardian_name' => 'required|string|max:200',
            'guardian_contact' => 'required|string|max:20',
            'guardian_relationship' => ['required', Rule::in(['Mother', 'Father', 'Grandmother', 'Grandfather', 'Aunt', 'Uncle', 'Older Sibling', 'Guardian', 'Other'])],
            'guardian_gender' => 'nullable|in:male,female',
            'diagnosis' => 'required|in:ADHD,Autism,Mental Disability,Cerebral Palsy,Blindness,Other',
            'diagnosis_notes' => 'nullable|string|max:500',

            // ── Enrollment fields ─────────────────────────────────
            'enrollment_date' => 'required|date',
            'school_year' => 'required|digits:4|integer|min:2000|max:2099',
            'remarks' => 'nullable|string|max:500',
        ]);

        // Bug fix: wrap in DB transaction so if enrollment creation fails,
        // the child record is also rolled back automatically
        \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $child = Child::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'date_of_birth' => $data['date_of_birth'],
                'gender' => $data['gender'],
                'address' => $data['address'],
                'guardian_name' => $data['guardian_name'],
                'guardian_contact' => $data['guardian_contact'],
                'guardian_relationship' => $data['guardian_relationship'],
                'guardian_gender' => $data['guardian_gender'] ?? null,
                'diagnosis' => $data['diagnosis'],
                'diagnosis_notes' => $data['diagnosis_notes'] ?? null,
                'status' => 'active',
                'staff_id' => auth()->id(),
            ]);

            $enrollment = Enrollment::create([
                'child_id' => $child->id,
                'enrollment_date' => $data['enrollment_date'],
                'school_year' => $data['school_year'],
                'remarks' => $data['remarks'] ?? null,
                'status' => 'pending',
            ]);

            // ── Notify all admins about the new pending enrollment ──
            $staffName = auth()->user()->name;
            $childName = $child->first_name . ' ' . $child->last_name;
            $schoolYear = $data['school_year'];

            \App\Models\Notification::create([
                'title' => '🔔 New Enrollment Pending Approval',
                'message' => "Staff {$staffName} submitted a new enrollment for {$childName} "
                    . "(School Year {$schoolYear}). Please review and approve or reject.",
                'recipient_group' => 'all',
                'sent_by' => auth()->id(),
                'sent_at' => Carbon::now(),
                'type' => 'enrollment_pending',
                'enrollment_id' => $enrollment->id,
            ]);
        });

        return redirect()->route('staff.enrollment.index')
            ->with('success', 'Child enrolled successfully! Awaiting admin approval.');
    }

    public function show(Enrollment $enrollment)
    {
        // Bug fix: removed staff ownership check — any staff can view any enrollment
        $enrollment->load(['child', 'approver:id,name']);
        return view('staff.enrollment.show', compact('enrollment'));
    }
}
