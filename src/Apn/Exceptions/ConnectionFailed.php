<?php

namespace Weelis\Notification\Apn\Exceptions;

use Exception;

class ConnectionFailed extends Exception
{
    /**
     * @param \Exception $exception
     *
     * @return \Weelis\Notification\Apn\Exceptions\ConnectionFailed
     */
    public static function create($exception)
    {
        return new static("Cannot connect to APNs: {$exception->getMessage()}", 0, $exception);
    }
}
