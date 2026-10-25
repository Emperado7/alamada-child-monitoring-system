<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'recipient_group',
        'recipient_id',
        'sent_by',
        'sent_at',
        'enrollment_id',    // added by migration 000001 (alter notifications)
        'type',             // added by migration 000001 (alter notifications) — e.g. 'general', 'enrollment_approved'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function readers()
    {
        return $this->belongsToMany(User::class, 'notification_reads', 'notification_id', 'user_id')
            ->withPivot('read_at')
            ->withTimestamps();
    }
}
