<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChildController extends Controller
{
    /**
     * GET /api/children
     * Admin/Staff: all children. Parent: only their own.
     */
    public function index(Request $request): JsonResponse
    {
        $user  = $request->user();
        $query = Child::with(['staff:id,name', 'parent:id,name', 'currentEnrollment']);

        if ($user->isParent()) {
            $query->where('parent_id', $user->id);
        } elseif ($user->isStaff()) {
            $query->where('staff_id', $user->id);
        }

        // Filters
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('diagnosis')) {
            $query->where('diagnosis', $request->diagnosis);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('guardian_name', 'like', "%{$request->search}%");
            });
        }

        $children = $query->orderBy('last_name')->paginate(15);

        // Append computed attributes
        $children->getCollection()->transform(function (Child $child) {
            $child->append(['full_name', 'age']);
            return $child;
        });

        return response()->json($children);
    }

    /**
     * POST /api/children
     */
    public function store(Request $request): JsonResponse
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
            'diagnosis'        => 'required|in:ADHD,Autism,Mental Disability,Cerebral Palsy,Blindness,Other',
            'diagnosis_notes'  => 'nullable|string',
            'staff_id'         => 'nullable|exists:users,id',
            'parent_id'        => 'nullable|exists:users,id',
            'photo'            => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('children/photos', 'public');
        }

        $child = Child::create($data);
        $child->append(['full_name', 'age']);

        return response()->json([
            'message' => 'Child record created successfully.',
            'child'   => $child->load(['staff:id,name', 'parent:id,name']),
        ], 201);
    }

    /**
     * GET /api/children/{id}
     */
    public function show(Request $request, Child $child): JsonResponse
    {
        $this->authorizeAccess($request, $child);

        $child->load(['staff:id,name,email', 'parent:id,name,email', 'enrollments', 'currentEnrollment']);
        $child->append(['full_name', 'age']);

        return response()->json($child);
    }

    /**
     * PUT /api/children/{id}
     */
    public function update(Request $request, Child $child): JsonResponse
    {
        $this->authorizeAccess($request, $child);

        $data = $request->validate([
            'first_name'       => 'sometimes|string|max:100',
            'last_name'        => 'sometimes|string|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'date_of_birth'    => 'sometimes|date|before:today',
            'gender'           => 'sometimes|in:male,female',
            'address'          => 'sometimes|string|max:255',
            'guardian_name'    => 'sometimes|string|max:200',
            'guardian_contact' => 'sometimes|string|max:20',
            'diagnosis'        => 'sometimes|in:ADHD,Autism,Mental Disability,Cerebral Palsy,Blindness,Other',
            'diagnosis_notes'  => 'nullable|string',
            'status'           => 'sometimes|in:active,inactive,graduated',
            'staff_id'         => 'nullable|exists:users,id',
            'parent_id'        => 'nullable|exists:users,id',
            'photo'            => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            // Remove old photo
            if ($child->photo) {
                Storage::disk('public')->delete($child->photo);
            }
            $data['photo'] = $request->file('photo')->store('children/photos', 'public');
        }

        $child->update($data);
        $child->append(['full_name', 'age']);

        return response()->json([
            'message' => 'Child record updated successfully.',
            'child'   => $child->fresh()->load(['staff:id,name', 'parent:id,name']),
        ]);
    }

    /**
     * DELETE /api/children/{id}
     */
    public function destroy(Child $child): JsonResponse
    {
        if ($child->photo) {
            Storage::disk('public')->delete($child->photo);
        }
        $child->delete();

        return response()->json(['message' => 'Child record deleted successfully.']);
    }

    /**
     * GET /api/children/{id}/activities
     */
    public function activities(Request $request, Child $child): JsonResponse
    {
        $this->authorizeAccess($request, $child);

        $activities = $child->activities()
            ->with('recorder:id,name')
            ->when($request->filled('date'), fn ($q) => $q->whereDate('activity_date', $request->date))
            ->orderBy('activity_date', 'desc')
            ->paginate(20);

        return response()->json($activities);
    }

    // ── Private helpers ───────────────────────────────────────────

    private function authorizeAccess(Request $request, Child $child): void
    {
        $user = $request->user();

        // Parents can only see their own child
        if ($user->isParent() && $child->parent_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        // Staff can only see children assigned to them
        if ($user->isStaff() && $child->staff_id !== $user->id) {
            abort(403, 'Access denied.');
        }
    }
}
