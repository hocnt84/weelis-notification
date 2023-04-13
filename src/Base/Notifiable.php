<?php

namespace Weelis\Notification\Base;

use Illuminate\Notifications\RoutesNotifications;
trait Notifiable
{
    use HasDatabaseNotifications, RoutesNotifications;
}
