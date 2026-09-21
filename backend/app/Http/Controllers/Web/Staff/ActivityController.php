<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Child;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    private const ACTIVITY_TYPES = [
        'Building Blocks', 'Logico Piccolo', 'Drawing and Coloring',
        'Writing Improvement', 'Socialization', 'Counting Numbers',
        'Identifying Alphabets', 'Identifying Shapes', 'Addition',
        'Subtraction', 'Maze Tracing', 'Puzzles', 'Other',
    ];

    public function index(Request $request)
    {
        $query = Activity::with(['child', 'recorder:id,name'])
            ->whereHas('child', fn($q) => $q->where('staff_id', auth()->id()));

        if ($request->filled('child_id'))     $query->where('child_id',     $request->child_id);
        if ($request->filled('activity_type')) $query->where('activity_type', $request->activity_type);
        if ($request->filled('date'))         $query->whereDate('activity_date', $request->date);

        $activities = $query->orderBy('activity_date', 'desc')->paginate(20)->withQueryString();

        $children = Child::where('staff_id', auth()->id())
            ->orderBy('last_name')->get(['id', 'first_name', 'last_name']);

        return view('staff.activities.index', compact('activities', 'children'), [
            'activityTypes' => self::ACTIVITY_TYPES,
        ]);
    }

    public function create()
    {
        $children = Child::where('staff_id', auth()->id())
            ->where('status', 'active')->orderBy('last_name')->get();

        return view('staff.activities.create', [
            'children'      => $children,
            'activityTypes' => self::ACTIVITY_TYPES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'child_id'          => 'required|exists:children,id',
            'activity_date'     => 'required|date',
            'activity_type'     => 'required|string',
            'completion_status' => 'required|in:completed,in_progress,not_started',
            'notes'             => 'nullable|string|max:500',
        ]);

        $child = Child::findOrFail($data['child_id']);
        if ($child->staff_id !== auth()->id()) abort(403);

        $data['recorded_by'] = auth()->id();
        Activity::create($data);

        return redirect()->route('staff.activities.index')
            ->with('success', 'Activity recorded successfully.');
    }

    public function edit(Activity $activity)
    {
        if ($activity->child->staff_id !== auth()->id()) abort(403);

        $children = Child::where('staff_id', auth()->id())
            ->where('status', 'active')->orderBy('last_name')->get();

        return view('staff.activities.edit', [
            'activity'      => $activity,
            'children'      => $children,
            'activityTypes' => self::ACTIVITY_TYPES,
        ]);
    }

    public function update(Request $request, Activity $activity)
    {
        if ($activity->child->staff_id !== auth()->id()) abort(403);

        $data = $request->validate([
            'activity_date'     => 'required|date',
            'activity_type'     => 'required|string',
            'completion_status' => 'required|in:completed,in_progress,not_started',
            'notes'             => 'nullable|string|max:500',
        ]);

        $activity->update($data);
        return redirect()->route('staff.activities.index')
            ->with('success', 'Activity updated.');
    }

    public function destroy(Activity $activity)
    {
        if ($activity->child->staff_id !== auth()->id()) abort(403);
        $activity->delete();
        return back()->with('success', 'Activity deleted.');
    }
}
