<?php

namespace Weelis\Notification\Base;

use Weelis\Notification\Model\NotificationReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Weelis\Notification\Fcm\FcmMessage;
use Weelis\Notification\Apn\ApnMessage;

class PushNotificationToDevice extends Notification implements ShouldQueue
{
	use Queueable;
	/**
	 * [$otp description]
	 * @var [type]
	 */
	public $report;

	/**
	 * Create a new notification instance.
	 *
	 * @param NotificationReport $report
	 */
	public function __construct(NotificationReport $report)
	{
		$this->report = $report;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		return [$notifiable->type];
	}

	public function toFcm($notifiable)
	{
		$notify = $this->report->notification->data;
		$data = [
			'title'         	=> $notify['title'],
			'body'          	=> $notify['body'],
			'sound'         	=> isset($notify['sound'])?$notify['sound']:'', // Optional
			'icon'          	=> isset($notify['icon'])?$notify['icon']:'', // Optional
			'type_slug'     	=> $notify['type_slug'], // Optional
			'type'          	=> $notify['type'], // Optional
			'id'            	=> $this->report->notifiable->id,
			'notification_id' 	=> $this->report->notification_id
		];
		if(isset($notify['custom'])) {
			$data = array_merge($data, $notify['custom']);
		}		
		$message = new FcmMessage();
		$message->report($this->report)->data($data);

		return $message;
	}

	public function toApn($notifiable)
	{
		$notify = $this->report->notification->data;
		$message = ApnMessage::create()
						->report($this->report)
						->badge(1)
						->title($notify['title'])
						->body($notify['body'])
						->custom('notification_id', $this->report->notification_id)
						->custom('id', $this->report->notifiable->id)
						->custom('type_slug', $notify['type_slug'])
						->custom('type', $notify['type']);
		if(isset($notify['custom'])) {
			foreach ($notify['custom'] as $key => $value) {
				$message = $message->custom($key, $value);
			}
		}		
		if(isset($notify['sound'])) {
			$message = $message->sound($notify['sound']);
		}
		return $message;
	}

	public function getScope($notifiable)
	{
		return $this->report->scope;
	}
}
