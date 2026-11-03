<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where(function ($q) {
            $q->whereIn('recipient_group', ['all', 'staff'])
                ->orWhere('recipient_id', auth()->id());
        })->with('sender:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $readIds = DB::table('notification_reads')
            ->where('user_id', auth()->id())->pluck('notification_id')->toArray();

        $notifications->getCollection()->transform(function ($n) use ($readIds) {
            $n->is_read = in_array($n->id, $readIds);
            return $n;
        });

        return view('staff.notifications.index', compact('notifications'));
    }

    public function markRead(Request $request, Notification $notification)
    {
        DB::table('notification_reads')->updateOrInsert(
            ['notification_id' => $notification->id, 'user_id' => auth()->id()],
            ['read_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'created_at' => Carbon::now()]
        );
        return back()->with('success', 'Notification marked as read.');
    }

    /** POST /staff/notifications/read-all */
    public function markAllRead()
    {
        $userId = auth()->id();
        $alreadyRead = DB::table('notification_reads')
            ->where('user_id', $userId)->pluck('notification_id')->toArray();

        $toMark = Notification::where(function ($q) use ($userId) {
            $q->whereIn('recipient_group', ['all', 'staff'])
                ->orWhere('recipient_id', $userId);
        })->whereNotIn('id', $alreadyRead)->pluck('id');

        $now = Carbon::now();
        $rows = $toMark->map(fn($id) => [
            'notification_id' => $id,
            'user_id' => $userId,
            'read_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        if (!empty($rows)) {
            DB::table('notification_reads')->insert($rows);
        }
        return back()->with('success', 'All notifications marked as read.');
    }

    /** GET /staff/notifications/unread-count (AJAX) */
    public function unreadCount()
    {
        $userId = auth()->id();
        $readIds = DB::table('notification_reads')
            ->where('user_id', $userId)->pluck('notification_id');

        $count = Notification::where(function ($q) use ($userId) {
            $q->whereIn('recipient_group', ['all', 'staff'])
                ->orWhere('recipient_id', $userId);
        })->whereNotIn('id', $readIds)->count();

        return response()->json(['count' => $count]);
    }

    /** GET /staff/notifications/recent (AJAX dropdown) */
    public function recent()
    {
        $userId = auth()->id();
        $readIds = DB::table('notification_reads')
            ->where('user_id', $userId)->pluck('notification_id')->toArray();

        $notifications = Notification::where(function ($q) use ($userId) {
            $q->whereIn('recipient_group', ['all', 'staff'])
                ->orWhere('recipient_id', $userId);
        })->with('sender:id,name')
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
