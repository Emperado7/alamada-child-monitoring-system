<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * GET /api/notifications
     * Returns notifications visible to the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Notification::query();

        if ($user->isParent()) {
            // Notifications broadcast to all/parents OR sent specifically to this user
            $query->where(function ($q) use ($user) {
                $q->whereIn('recipient_group', ['all', 'parents'])
                  ->orWhere('recipient_id', $user->id);
            });
        } elseif ($user->isStaff()) {
            $query->where(function ($q) use ($user) {
                $q->whereIn('recipient_group', ['all', 'staff'])
                  ->orWhere('recipient_id', $user->id);
            });
        }
        // Admin sees all

        $notifications = $query
            ->with('sender:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Attach read status for each notification
        $readIds = DB::table('notification_reads')
            ->where('user_id', $user->id)
            ->pluck('notification_id')
            ->toArray();

        $notifications->getCollection()->transform(function ($n) use ($readIds) {
            $n->is_read = in_array($n->id, $readIds);
            return $n;
        });

        return response()->json($notifications);
    }

    /**
     * POST /api/notifications  (Admin/Staff only)
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'message'         => 'required|string',
            'recipient_group' => 'required|in:all,parents,staff,specific',
            'recipient_id'    => 'required_if:recipient_group,specific|nullable|exists:users,id',
        ]);

        $data['sent_by'] = $request->user()->id;
        $data['sent_at'] = Carbon::now();

        $notification = Notification::create($data);

        return response()->json([
            'message'      => 'Notification sent successfully.',
            'notification' => $notification->load('sender:id,name'),
        ], 201);
    }

    /**
     * GET /api/notifications/{id}
     */
    public function show(Request $request, Notification $notification): JsonResponse
    {
        // Auto-mark as read when fetched
        DB::table('notification_reads')->updateOrInsert(
            ['notification_id' => $notification->id, 'user_id' => $request->user()->id],
            ['read_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'created_at' => Carbon::now()]
        );

        return response()->json($notification->load('sender:id,name'));
    }

    /**
     * POST /api/notifications/{id}/read
     * Explicitly mark a notification as read.
     */
    public function markRead(Request $request, Notification $notification): JsonResponse
    {
        DB::table('notification_reads')->updateOrInsert(
            ['notification_id' => $notification->id, 'user_id' => $request->user()->id],
            ['read_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'created_at' => Carbon::now()]
        );

        return response()->json(['message' => 'Notification marked as read.']);
    }

    /**
     * POST /api/notifications/read-all
     * Mark all of the user's notifications as read.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get all notification IDs visible to this user
        $ids = Notification::where(function ($q) use ($user) {
            $q->whereIn('recipient_group', ['all', $user->role . 's'])
              ->orWhere('recipient_id', $user->id);
        })->pluck('id');

        $now = Carbon::now();
        foreach ($ids as $id) {
            DB::table('notification_reads')->updateOrInsert(
                ['notification_id' => $id, 'user_id' => $user->id],
                ['read_at' => $now, 'updated_at' => $now, 'created_at' => $now]
            );
        }

        return response()->json(['message' => 'All notifications marked as read.']);
    }

    /**
     * PUT /api/notifications/{id}  (Admin only)
     */
    public function update(Request $request, Notification $notification): JsonResponse
    {
        $data = $request->validate([
            'title'   => 'sometimes|string|max:255',
            'message' => 'sometimes|string',
        ]);

        $notification->update($data);

        return response()->json([
            'message'      => 'Notification updated.',
            'notification' => $notification->fresh()->load('sender:id,name'),
        ]);
    }

    /**
     * DELETE /api/notifications/{id}  (Admin only)
     */
    public function destroy(Notification $notification): JsonResponse
    {
        $notification->delete();
        return response()->json(['message' => 'Notification deleted.']);
    }

    /**
     * GET /api/notifications/unread-count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $user = $request->user();

        $total = Notification::where(function ($q) use ($user) {
            $q->whereIn('recipient_group', ['all', $user->role . 's'])
              ->orWhere('recipient_id', $user->id);
        })->count();

        $read = DB::table('notification_reads')
            ->where('user_id', $user->id)
            ->count();

        return response()->json(['unread_count' => max(0, $total - $read)]);
    }
}
