<?php

namespace Weelis\Notification\Base;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Weelis\Notification\Fcm\EsmsMessage;
use Illuminate\Notifications\Messages\MailMessage;

class NotificationToUser extends Notification implements ShouldQueue
{
	use Queueable;
	/**
	 * Notification array
	 * @var array
	 */
	public $data;

	/**
	 * Notifitable model
	 * @var Illuminate\Database\Eloquent\Model
	 */
	public $notifitable;

	/**
	 * Create a new notification instance.
	 *
	 * @param array $data
	 */
	public function __construct($data, $notifitable = null)
	{
		$this->data = $data;
		$this->notifitable = $notifitable;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		if (!isset($this->notifitable)) {
			$this->notifitable = $notifiable;
		}
		$channels = ['db'];
		if (in_array('esms', $this->data['types'])) {
			$channels[] = 'esms';
		}
		if (in_array('email', $this->data['types'])) {
			$channels[] = 'mail';
		}

		return $channels;
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @param  mixed $notifiable
	 * @return array
	 */
	public function toDatabase($notifiable)
	{
		if (in_array('apn', $this->data['types'])) {
			$devices = $notifiable->devices()->where('type', 'apn')->where('scope', $this->data['scope'])->whereNotNull('push_token')->get();
			foreach ($devices as $device) {
				$report = $this->notifitable->reports()->create([
					"user_id"         => $notifiable->id,
					"scope"           => $this->data['scope'],
					"channel"         => 'apn',
					"notification_id" => $this->id,
					"send_at"         => Carbon::now()
				]);
				$device->notify(new PushNotificationToDevice($report));
			}
		}
		if (in_array('fcm', $this->data['types'])) {
			$devices = $notifiable->devices()->where('type', 'fcm')->where('scope', $this->data['scope'])->whereNotNull('push_token')->get();
			foreach ($devices as $device) {
				$report = $this->notifitable->reports()->create([
					"user_id"         => $notifiable->id,
					"scope"           => $this->data['scope'],
					"channel"         => 'fcm',
					"notification_id" => $this->id,
					"send_at"         => Carbon::now()
				]);
				$device->notify(new PushNotificationToDevice($report));
			}
		}
		$this->data["id"] = $this->notifitable->getKey();

		return $this->data;
	}

	public function toEsms($notifiable)
	{
		$message = new EsmsMessage();
		$report = $this->notifitable->reports()->create([
			"user_id"         => $notifiable->id,
			"scope"           => $this->data['scope'],
			"channel"         => 'esms',
			"notification_id" => $this->id,
			"send_at"         => Carbon::now()
		]);

		return $message->report($report)->body($this->data['body']);
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed $notifiable
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable)
	{
		$report = $this->notifitable->reports()->create([
			"user_id"         => $notifiable->id,
			"scope"           => $this->data['scope'],
			"channel"         => 'email',
			"notification_id" => $this->id,
			"send_at"         => Carbon::now()
		]);
		$mail_message = (new MailMessage)
			->subject($this->data["title"]);
		if (isset($this->data["email_view"])) {
			$mail_message = $mail_message->view($this->data["email_view"], isset($this->data['custom']) ? $this->data['custom'] : []);
		} else {
			$mail_message = $mail_message->line($this->data["body"]);
		}

		return $mail_message;
	}
}
