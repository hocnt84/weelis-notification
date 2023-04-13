<?php

namespace Weelis\Notification\Db;

use RuntimeException;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Channels\DatabaseChannel as DBChannel;

class DatabaseChannel extends DBChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function send($notifiable, Notification $notification)
    {
        $data = $this->getData($notifiable, $notification);
        return $notifiable->routeNotificationFor('database')->create([
            'id'            => $notification->id,
            'scope'         => $data['scope'],
            "type_slug"     => isset($data['type_slug'])?$data['type_slug']:null,
		    "type_id"       => $data['id'],
            'type'          => get_class($notification),
            'data'          => $data,
            'read_at'       => null,
        ]);
    }
}
