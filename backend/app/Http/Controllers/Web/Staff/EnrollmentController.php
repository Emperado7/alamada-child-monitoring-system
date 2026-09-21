<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::with(['child', 'approver:id,name'])
            ->whereHas('child', fn($q) => $q->where('staff_id', auth()->id()));

        if ($request->filled('status'))
            $query->where('status', $request->status);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('child', fn($q) =>
                $q->where('first_name', 'like', "%$s%")
                  ->orWhere('last_name',  'like', "%$s%")
                  ->orWhere('guardian_name','like',"%$s%")
                  ->orWhereRaw("CONCAT('CHD-', LPAD(id,3,'0')) LIKE ?", ["%$s%"])
            );
        }

        $enrollments = $query->orderBy('created_at', 'desc')
                             ->paginate(10)->withQueryString();

        return view('staff.enrollment.index', compact('enrollments'));
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

            // ── Enrollment fields ─────────────────────────────────
            'enrollment_date'  => 'required|date',
            'school_year'      => 'required|digits:4|integer|min:2000|max:2099',
            'remarks'          => 'nullable|string|max:500',
        ]);

        // Create the child record assigned to this staff
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
            'staff_id'         => auth()->id(),
        ]);

        // Check if child already has enrollment for this year
        $exists = Enrollment::where('child_id', $child->id)
            ->where('school_year', $data['school_year'])
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($exists) {
            $child->delete(); // rollback child creation
            return back()
                ->withInput()
                ->withErrors(['school_year' => 'This child already has an enrollment for that school year.']);
        }

        // Create the enrollment (pending admin approval)
        Enrollment::create([
            'child_id'        => $child->id,
            'enrollment_date' => $data['enrollment_date'],
            'school_year'     => $data['school_year'],
            'remarks'         => $data['remarks'] ?? null,
            'status'          => 'pending',
        ]);

        return redirect()->route('staff.enrollment.index')
            ->with('success', 'Child enrolled successfully! Awaiting admin approval.');
    }

    public function show(Enrollment $enrollment)
    {
        if ($enrollment->child->staff_id !== auth()->id()) abort(403);
        $enrollment->load(['child', 'approver:id,name']);
        return view('staff.enrollment.show', compact('enrollment'));
    }
}
