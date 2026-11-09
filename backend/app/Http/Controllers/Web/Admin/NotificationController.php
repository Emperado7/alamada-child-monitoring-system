<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::with('sender:id,name');

        if ($request->filled('recipient_group'))
            $query->where('recipient_group', $request->recipient_group);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('title', 'ilike', "%$s%")
                ->orWhere('message', 'ilike', "%$s%"));
        }
        if ($request->filled('date_from'))
            $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))
            $query->whereDate('created_at', '<=', $request->date_to);

        // Fix: read_status filter now works
        $readIds = \Illuminate\Support\Facades\DB::table('notification_reads')
            ->where('user_id', auth()->id())
            ->pluck('notification_id')
            ->toArray();

        if ($request->filled('read_status')) {
            if ($request->read_status === 'read') {
                $query->whereIn('id', $readIds);
            } elseif ($request->read_status === 'unread') {
                $query->whereNotIn('id', $readIds);
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(10)->withQueryString();

        // Attach is_read flag
        $notifications->getCollection()->transform(function ($n) use ($readIds) {
            $n->is_read = in_array($n->id, $readIds);
            return $n;
        });

        // ── Pending enrollments that need approval ────────────────
        $pendingEnrollments = \App\Models\Enrollment::with(['child'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // ── Unread count by type ──────────────────────────────────
        $unreadPending = Notification::where('type', 'enrollment_pending')
            ->whereNotIn('id', $readIds)->count();

        return view('admin.notifications.index', compact(
            'notifications',
            'pendingEnrollments',
            'unreadPending'
        ));
    }

    public function create()
    {
        return view('admin.notifications.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'recipient_group' => 'required|in:all,parents,staff,specific',
            'recipient_id' => 'required_if:recipient_group,specific|nullable|exists:users,id',
        ]);

        $data['sent_by'] = auth()->id();
        $data['sent_at'] = Carbon::now();

        Notification::create($data);
        return redirect()->route('admin.notifications.index')->with('success', 'Notification sent successfully.');
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return back()->with('success', 'Notification deleted.');
    }

    /** POST /admin/notifications/{notification}/read */
    public function markRead(Notification $notification)
    {
        \Illuminate\Support\Facades\DB::table('notification_reads')->updateOrInsert(
            ['notification_id' => $notification->id, 'user_id' => auth()->id()],
            ['read_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'created_at' => Carbon::now()]
        );
        return back()->with('success', 'Notification marked as read.');
    }

    /** POST /admin/notifications/read-all */
    public function markAllRead()
    {
        $userId = auth()->id();
        $alreadyRead = \Illuminate\Support\Facades\DB::table('notification_reads')
            ->where('user_id', $userId)->pluck('notification_id')->toArray();

        $toMark = Notification::whereNotIn('id', $alreadyRead)->pluck('id');
        $now = Carbon::now();
        $rows = $toMark->map(fn($id) => [
            'notification_id' => $id,
            'user_id' => $userId,
            'read_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        if (!empty($rows)) {
            \Illuminate\Support\Facades\DB::table('notification_reads')->insert($rows);
        }
        return back()->with('success', 'All notifications marked as read.');
    }

    /** GET /admin/notifications/unread-count (AJAX) */
    public function unreadCount()
    {
        $readIds = \Illuminate\Support\Facades\DB::table('notification_reads')
            ->where('user_id', auth()->id())->pluck('notification_id');
        $count = Notification::whereNotIn('id', $readIds)->count();
        return response()->json(['count' => $count]);
    }

    /** GET /admin/notifications/recent (AJAX dropdown) */
    public function recent()
    {
        $readIds = \Illuminate\Support\Facades\DB::table('notification_reads')
            ->where('user_id', auth()->id())->pluck('notification_id')->toArray();

        $notifications = Notification::with('sender:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'message' => \Illuminate\Support\Str::limit($n->message, 60),
                'is_read' => in_array($n->id, $readIds),
                'time' => $n->created_at->diffForHumans(),
                'sender' => $n->sender?->name ?? 'System',
            ]);

        return response()->json($notifications);
    }
}
