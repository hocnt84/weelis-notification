<?php

namespace Weelis\Notification\Model;


trait NotificationModel
{
    public function reports(){
        return $this->morphMany(NotificationReport::class, 'notifiable');
    }
}
