<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::with('sender:id,name');

        if ($request->filled('recipient_group'))
            $query->where('recipient_group', $request->recipient_group);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('title','like',"%$s%")
                ->orWhere('message','like',"%$s%"));
        }
        if ($request->filled('date_from'))
            $query->whereDate('created_at','>=',$request->date_from);
        if ($request->filled('date_to'))
            $query->whereDate('created_at','<=',$request->date_to);

        $notifications = $query->orderBy('created_at','desc')
                               ->paginate(10)->withQueryString();

        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        return view('admin.notifications.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'message'         => 'required|string',
            'recipient_group' => 'required|in:all,parents,staff,specific',
            'recipient_id'    => 'required_if:recipient_group,specific|nullable|exists:users,id',
        ]);

        $data['sent_by'] = auth()->id();
        $data['sent_at'] = Carbon::now();

        Notification::create($data);
        return redirect()->route('admin.notifications.index')->with('success','Notification sent successfully.');
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return back()->with('success','Notification deleted.');
    }
}
