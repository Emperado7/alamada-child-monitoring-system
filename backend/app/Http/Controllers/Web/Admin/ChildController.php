<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChildController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50]) ? $perPage : 10;

        $query = Child::with(['staff:id,name', 'parent:id,name', 'currentEnrollment']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('first_name','like',"%$s%")
                ->orWhere('last_name','like',"%$s%")
                ->orWhere('guardian_name','like',"%$s%"));
        }
        if ($request->filled('diagnosis'))  $query->where('diagnosis',  $request->diagnosis);
        if ($request->filled('gender'))     $query->where('gender',     $request->gender);
        if ($request->filled('status'))     $query->where('status',     $request->status);

        $children  = $query->orderBy('last_name')->paginate($perPage)->withQueryString();
        $staffList = User::where('role','staff')->where('status','active')->orderBy('name')->get();

        return view('admin.children.index', compact('children', 'staffList'));
    }

    public function create()
    {
        $staffList  = User::where('role','staff')->where('status','active')->orderBy('name')->get();
        $parentList = User::where('role','parent')->where('status','active')->orderBy('name')->get();
        return view('admin.children.create', compact('staffList', 'parentList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'date_of_birth'    => 'required|date|before:today',
            'gender'           => 'required|in:male,female',
            'address'          => 'required|string|max:255',
            'guardian_name'    => 'required|string|max:200',
            'guardian_contact' => 'required|string|max:20',
            'diagnosis'        => 'required|string',
            'diagnosis_notes'  => 'nullable|string',
            'staff_id'         => 'nullable|exists:users,id',
            'parent_id'        => 'nullable|exists:users,id',
            'photo'            => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('children/photos', 'public');
        }

        Child::create($data);
        return redirect()->route('admin.children.index')->with('success', 'Child record created successfully.');
    }

    public function show(Child $child)
    {
        $child->load(['staff:id,name,email', 'parent:id,name,email',
                      'enrollments.approver:id,name',
                      'attendances' => fn($q) => $q->orderBy('attendance_date','desc')->take(30),
                      'activities'  => fn($q) => $q->orderBy('activity_date','desc')->take(20)]);
        return view('admin.children.show', compact('child'));
    }

    public function edit(Child $child)
    {
        $staffList  = User::where('role','staff')->where('status','active')->orderBy('name')->get();
        $parentList = User::where('role','parent')->where('status','active')->orderBy('name')->get();
        return view('admin.children.edit', compact('child', 'staffList', 'parentList'));
    }

    public function update(Request $request, Child $child)
    {
        $data = $request->validate([
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'date_of_birth'    => 'required|date|before:today',
            'gender'           => 'required|in:male,female',
            'address'          => 'required|string|max:255',
            'guardian_name'    => 'required|string|max:200',
            'guardian_contact' => 'required|string|max:20',
            'diagnosis'        => 'required|string',
            'diagnosis_notes'  => 'nullable|string',
            'status'           => 'required|in:active,inactive,graduated',
            'staff_id'         => 'nullable|exists:users,id',
            'parent_id'        => 'nullable|exists:users,id',
            'photo'            => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($child->photo) Storage::disk('public')->delete($child->photo);
            $data['photo'] = $request->file('photo')->store('children/photos', 'public');
        }

        $child->update($data);
        return redirect()->route('admin.children.index')->with('success', 'Child record updated.');
    }

    public function destroy(Child $child)
    {
        if ($child->photo) Storage::disk('public')->delete($child->photo);
        $child->delete();
        return redirect()->route('admin.children.index')->with('success', 'Child record deleted.');
    }
}
