<?php

namespace Weelis\Notification\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification as DBNotification;

class DatabaseNotification extends DBNotification
{
	protected static function boot()
	{
		parent::boot();

		static::deleting(function ($user) {
			// Clean up
			$user->reports()->delete();
		});
	}

    public function reports()
    {
        return $this->hasMany(NotificationReport::class, 'notifiable_id');
    }
}
