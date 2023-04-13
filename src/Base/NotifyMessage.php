<?php

namespace Weelis\Notification\Base;

abstract class NotifyMessage
{
    /**
     * The report of the notification.
     *
     * @var \Weelis\Notification\Model\NotificationReport
     */
    private $report;

    /**
     * Set report model of the notification.
     *
     * @param \Weelis\Notification\Model\NotificationReport $report
     *
     * @return $this
     */
    public function report($report)
    {
        $this->report = $report;

        return $this;
    }

    /**
     * @return \Weelis\Notification\Model\NotificationReport
     */
    public function getReport()
    {
        return $this->report;
    }
}
