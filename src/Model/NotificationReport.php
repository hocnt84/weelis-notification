<?php

namespace Weelis\Notification\Model;

use Illuminate\Database\Eloquent\Model;

class NotificationReport extends Model
{
    protected $table = "notification_reports";
    
    protected $fillable = [
        'user_id',
        'scope',
        'channel',
        'notification_id',
        'send_log',
        'send_at',
        'receive_at',
        'read_at',
        'note'
    ];
    public $timestamps = false;
    protected $dates = [
        'send_at',
        'receive_at',
        'read_at'
    ];

    public function user()
    {
        return $this->belongsTo(config("auth.providers.users.model"), 'user_id');
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function notification()
    {
        return $this->belongsTo(DatabaseNotification::class, "notification_id");
    }
}
