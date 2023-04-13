<?php

namespace Weelis\Notification\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Devices extends Model
{
	use Notifiable;

    protected $table = 'devices';

 	protected $fillable = [
		'os', 'device', 'type', 'did', 'push_token', 'user_id', 'scope'
	];
	public function user(){
		return $this->belongsTo(config("auth.providers.users.model"), 'user_id');
	}

	public function routeNotificationForApn()
	{
		return $this->push_token;
	}

	public function routeNotificationForFcm()
	{
		return $this->push_token;
	}
}
