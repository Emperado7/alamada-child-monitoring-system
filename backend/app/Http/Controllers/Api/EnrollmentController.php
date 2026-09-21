<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EnrollmentController extends Controller
{
    /**
     * GET /api/enrollments
     * Supports filters: status, school_year, child_id, search (child name)
     */
    public function index(Request $request): JsonResponse
    {
        $user  = $request->user();
        $query = Enrollment::with(['child', 'approver:id,name'])
            ->join('children', 'enrollments.child_id', '=', 'children.id')
            ->select('enrollments.*');

        // Scope by role
        if ($user->isParent()) {
            $query->where('children.parent_id', $user->id);
        } elseif ($user->isStaff()) {
            $query->where('children.staff_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('enrollments.status', $request->status);
        }
        if ($request->filled('school_year')) {
            $query->where('enrollments.school_year', $request->school_year);
        }
        if ($request->filled('child_id')) {
            $query->where('enrollments.child_id', $request->child_id);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('children.first_name', 'like', "%{$request->search}%")
                  ->orWhere('children.last_name', 'like', "%{$request->search}%");
            });
        }

        $enrollments = $query->orderBy('enrollments.created_at', 'desc')->paginate(15);

        return response()->json($enrollments);
    }

    /**
     * POST /api/enrollments
     * Staff creates an enrollment for a child (status = pending).
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'child_id'        => 'required|exists:children,id',
            'enrollment_date' => 'required|date',
            'school_year'     => 'required|digits:4|integer|min:2000|max:2099',
            'remarks'         => 'nullable|string',
        ]);

        // Check if child already has an active enrollment for this year
        $exists = Enrollment::where('child_id', $data['child_id'])
            ->where('school_year', $data['school_year'])
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'This child already has an enrollment for the selected school year.',
            ], 422);
        }

        $enrollment = Enrollment::create($data);

        return response()->json([
            'message'    => 'Enrollment submitted successfully. Awaiting admin approval.',
            'enrollment' => $enrollment->load('child'),
        ], 201);
    }

    /**
     * GET /api/enrollments/{id}
     */
    public function show(Enrollment $enrollment): JsonResponse
    {
        return response()->json(
            $enrollment->load(['child', 'approver:id,name'])
        );
    }

    /**
     * PUT /api/enrollments/{id}
     */
    public function update(Request $request, Enrollment $enrollment): JsonResponse
    {
        $data = $request->validate([
            'enrollment_date' => 'sometimes|date',
            'school_year'     => 'sometimes|digits:4|integer|min:2000|max:2099',
            'remarks'         => 'nullable|string',
        ]);

        if ($enrollment->status !== 'pending') {
            return response()->json(['message' => 'Only pending enrollments can be edited.'], 422);
        }

        $enrollment->update($data);

        return response()->json([
            'message'    => 'Enrollment updated successfully.',
            'enrollment' => $enrollment->fresh()->load('child'),
        ]);
    }

    /**
     * PUT /api/enrollments/{id}/approve  (Admin only)
     */
    public function approve(Request $request, Enrollment $enrollment): JsonResponse
    {
        if ($enrollment->status !== 'pending') {
            return response()->json(['message' => 'This enrollment is not pending.'], 422);
        }

        $enrollment->update([
            'status'      => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => Carbon::now(),
        ]);

        // Activate the child if not already
        $enrollment->child->update(['status' => 'active']);

        return response()->json([
            'message'    => 'Enrollment approved successfully.',
            'enrollment' => $enrollment->fresh()->load(['child', 'approver:id,name']),
        ]);
    }

    /**
     * PUT /api/enrollments/{id}/reject  (Admin only)
     */
    public function reject(Request $request, Enrollment $enrollment): JsonResponse
    {
        $request->validate(['remarks' => 'nullable|string']);

        if ($enrollment->status !== 'pending') {
            return response()->json(['message' => 'This enrollment is not pending.'], 422);
        }

        $enrollment->update([
            'status'      => 'rejected',
            'approved_by' => $request->user()->id,
            'approved_at' => Carbon::now(),
            'remarks'     => $request->remarks ?? $enrollment->remarks,
        ]);

        return response()->json([
            'message'    => 'Enrollment rejected.',
            'enrollment' => $enrollment->fresh()->load(['child', 'approver:id,name']),
        ]);
    }

    /**
     * DELETE /api/enrollments/{id}  (Admin only)
     */
    public function destroy(Enrollment $enrollment): JsonResponse
    {
        $enrollment->delete();
        return response()->json(['message' => 'Enrollment record deleted.']);
    }
}
