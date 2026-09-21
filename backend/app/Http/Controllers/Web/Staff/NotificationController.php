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
}
