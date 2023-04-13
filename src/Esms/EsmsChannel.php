<?php
/**
 * Created by PhpStorm.
 * User: simonnguyen
 * Date: 11/1/17
 * Time: 1:58 PM
 */

namespace Weelis\Notification\Esms;

use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Notifications\Notification;
use NotificationHelper;

class EsmsChannel
{
	public function __construct()
	{
		$this->client = new GuzzleHttpClient();
		$this->ap_id = config('notification.esms.esms_api_key');
		$this->ap_secret = config('notification.esms.esms_secret_key');
		$this->ap_type = config('notification.esms.esms_sms_type');
		$this->ap_brand = config('notification.esms.esms_brand_name');
		$this->api_url = config('notification.esms.esms_url');
	}

	/**
	 * Send the given notification.
	 *
	 * @param  mixed $notifiable
	 * @param  \Illuminate\Notifications\Notification $notification
	 * @return void
	 */
	public function send($notifiable, Notification $notification)
	{
		if (!$phone = $notifiable->routeNotificationFor('Esms')) {
			return;
		}

		$message = $notification->toEsms($notifiable);

		if (!$message) {
			return;
		}
		
		if (NotificationHelper::increaseSmsRetry($phone) ) {
			$request = http_build_query([
				"Phone"     => $phone,
				"Content"   => $message->getBody(),
				"ApiKey"    => $this->ap_id,
				"SecretKey" => $this->ap_secret,
				"SmsType"   => $this->ap_type,
				"Brandname" => $this->ap_brand
			]);
			$url = $this->api_url . '?' .$request;
			$response = $this->client->get($url);

			if($report = $message->getReport()) {
				$report->update([
					'send_log' => $response->getBody()
				]);
			}
		} else {
			if($report = $message->getReport()) {
				$report->update([
					'send_log' => "SMS limit exceeded"
				]);
			}
		}
	}
}