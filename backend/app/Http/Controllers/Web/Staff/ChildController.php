<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\Child;
use Illuminate\Http\Request;

class ChildController extends Controller
{
    public function index(Request $request)
    {
        $query = Child::with(['currentEnrollment'])
            ->where('staff_id', auth()->id());

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('first_name', 'like', "%$s%")
                ->orWhere('last_name',  'like', "%$s%")
                ->orWhere('guardian_name', 'like', "%$s%")
                ->orWhereRaw("CONCAT('CHD-', LPAD(id,3,'0')) LIKE ?", ["%$s%"])
            );
        }
        if ($request->filled('gender')) $query->where('gender', $request->gender);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('diagnosis')) $query->where('diagnosis', $request->diagnosis);

        $children = $query->orderBy('last_name')->paginate(10)->withQueryString();

        return view('staff.children.index', compact('children'));
    }

    public function show(Child $child)
    {
        // Staff can only view their own assigned children
        if ($child->staff_id !== auth()->id()) {
            abort(403, 'Access denied.');
        }

        $child->load([
            'parent:id,name,email,phone',
            'enrollments.approver:id,name',
            'attendances' => fn($q) => $q->orderBy('attendance_date', 'desc')->take(30),
            'activities'  => fn($q) => $q->orderBy('activity_date', 'desc')->take(20),
        ]);

        return view('staff.children.show', compact('child'));
    }
}
