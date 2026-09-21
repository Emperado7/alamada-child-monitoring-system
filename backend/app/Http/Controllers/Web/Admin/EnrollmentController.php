<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50]) ? $perPage : 10;

        $query = Enrollment::with(['child', 'approver:id,name']);

        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('school_year')) $query->where('school_year', $request->school_year);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('child', fn($q) =>
                $q->where('first_name', 'like', "%$s%")
                  ->orWhere('last_name',  'like', "%$s%")
                  ->orWhere('guardian_name','like',"%$s%"));
        }

        $enrollments = $query->orderBy('created_at', 'desc')
                             ->paginate($perPage)->withQueryString();
        $years = Enrollment::selectRaw('DISTINCT school_year')
            ->orderBy('school_year', 'desc')->pluck('school_year');

        return view('admin.enrollment.index', compact('enrollments', 'years'));
    }

    public function create()
    {
        $staffList = User::where('role', 'staff')
            ->where('status', 'active')
            ->orderBy('name')->get();

        return view('admin.enrollment.create', compact('staffList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // ── Child fields ──────────────────────────────────────
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'date_of_birth'    => 'required|date|before:today',
            'gender'           => 'required|in:male,female',
            'address'          => 'required|string|max:255',
            'guardian_name'    => 'required|string|max:200',
            'guardian_contact' => 'required|string|max:20',
            'diagnosis'        => 'required|in:ADHD,Autism,Mental Disability,Cerebral Palsy,Blindness,Other',
            'diagnosis_notes'  => 'nullable|string|max:500',
            'staff_id'         => 'nullable|exists:users,id',

            // ── Enrollment fields ─────────────────────────────────
            'enrollment_date'  => 'required|date',
            'school_year'      => 'required|digits:4|integer|min:2000|max:2099',
            'remarks'          => 'nullable|string|max:500',
            'auto_approve'     => 'nullable|boolean',
        ]);

        // Create the child
        $child = Child::create([
            'first_name'       => $data['first_name'],
            'last_name'        => $data['last_name'],
            'middle_name'      => $data['middle_name'] ?? null,
            'date_of_birth'    => $data['date_of_birth'],
            'gender'           => $data['gender'],
            'address'          => $data['address'],
            'guardian_name'    => $data['guardian_name'],
            'guardian_contact' => $data['guardian_contact'],
            'diagnosis'        => $data['diagnosis'],
            'diagnosis_notes'  => $data['diagnosis_notes'] ?? null,
            'status'           => 'active',
            'staff_id'         => $data['staff_id'] ?? null,
        ]);

        // Determine enrollment status
        $autoApprove = $request->boolean('auto_approve');
        $status      = $autoApprove ? 'approved' : 'pending';

        $enrollment = Enrollment::create([
            'child_id'        => $child->id,
            'enrollment_date' => $data['enrollment_date'],
            'school_year'     => $data['school_year'],
            'remarks'         => $data['remarks'] ?? null,
            'status'          => $status,
            'approved_by'     => $autoApprove ? auth()->id() : null,
            'approved_at'     => $autoApprove ? Carbon::now() : null,
        ]);

        $msg = $autoApprove
            ? 'Child enrolled and approved successfully.'
            : 'Child enrolled successfully. Enrollment is pending approval.';

        return redirect()->route('admin.enrollment.index')->with('success', $msg);
    }

    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['child', 'approver:id,name']);
        return view('admin.enrollment.show', compact('enrollment'));
    }

    public function approve(Request $request, Enrollment $enrollment)
    {
        if ($enrollment->status !== 'pending') {
            return back()->with('error', 'Only pending enrollments can be approved.');
        }
        $enrollment->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now(),
        ]);
        $enrollment->child->update(['status' => 'active']);
        return back()->with('success', 'Enrollment approved successfully.');
    }

    public function reject(Request $request, Enrollment $enrollment)
    {
        $request->validate(['remarks' => 'nullable|string|max:500']);
        if ($enrollment->status !== 'pending') {
            return back()->with('error', 'Only pending enrollments can be rejected.');
        }
        $enrollment->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now(),
            'remarks'     => $request->remarks,
        ]);
        return back()->with('success', 'Enrollment rejected.');
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();
        return redirect()->route('admin.enrollment.index')->with('success', 'Enrollment deleted.');
    }
}
