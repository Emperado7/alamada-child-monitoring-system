<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * GET /api/activities
     */
    public function index(Request $request): JsonResponse
    {
        $user  = $request->user();
        $query = Activity::with(['child', 'recorder:id,name'])
            ->join('children', 'activities.child_id', '=', 'children.id')
            ->select('activities.*');

        if ($user->isParent()) {
            $query->where('children.parent_id', $user->id);
        } elseif ($user->isStaff()) {
            $query->where('children.staff_id', $user->id);
        }

        if ($request->filled('child_id')) {
            $query->where('activities.child_id', $request->child_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('activities.activity_date', $request->date);
        }
        if ($request->filled('activity_type')) {
            $query->where('activities.activity_type', $request->activity_type);
        }

        $activities = $query->orderBy('activities.activity_date', 'desc')->paginate(20);

        return response()->json($activities);
    }

    /**
     * POST /api/activities
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'child_id'          => 'required|exists:children,id',
            'activity_date'     => 'required|date',
            'activity_type'     => 'required|string',
            'completion_status' => 'required|in:completed,in_progress,not_started',
            'notes'             => 'nullable|string',
        ]);

        $data['recorded_by'] = $request->user()->id;

        $activity = Activity::create($data);

        return response()->json([
            'message'  => 'Activity recorded successfully.',
            'activity' => $activity->load('child'),
        ], 201);
    }

    /**
     * GET /api/activities/{id}
     */
    public function show(Activity $activity): JsonResponse
    {
        return response()->json($activity->load(['child', 'recorder:id,name']));
    }

    /**
     * PUT /api/activities/{id}
     */
    public function update(Request $request, Activity $activity): JsonResponse
    {
        $data = $request->validate([
            'activity_type'     => 'sometimes|string',
            'completion_status' => 'sometimes|in:completed,in_progress,not_started',
            'notes'             => 'nullable|string',
        ]);

        $activity->update($data);

        return response()->json([
            'message'  => 'Activity updated.',
            'activity' => $activity->fresh()->load(['child', 'recorder:id,name']),
        ]);
    }

    /**
     * DELETE /api/activities/{id}
     */
    public function destroy(Activity $activity): JsonResponse
    {
        $activity->delete();
        return response()->json(['message' => 'Activity deleted.']);
    }
}
