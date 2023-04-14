<?php

namespace Weelis\Notification\Esms;

use Weelis\Notification\Base\NotifyMessage;

/**
 * Class FcmMessage
 * @package Weelis\Notification\Fcm
 */
class EsmsMessage extends NotifyMessage
{
    /**
     * @var string
     */
    private $body;

    /**
     * Set the alert message of the notification.
     *
     * @param string $body
     *
     * @return $this
     */
    public function body($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
}
