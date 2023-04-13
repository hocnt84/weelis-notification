<?php

namespace Weelis\Notification\Facade;

use Illuminate\Support\Facades\Facade;

class NotificationHelper extends Facade
{	
	protected static function getFacadeAccessor(){return 'notification.helper';}
}