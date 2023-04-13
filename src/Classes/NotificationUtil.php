<?php

namespace Weelis\Notification\Classes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Cache;
use Carbon\Carbon;
use Weelis\Notification\Model\Devices;

class NotificationUtil
{	
    private $sms_cache_prefix;
	public function __construct()
    {
        $this->sms_cache_prefix = "phone_secure:";
    }

    public function genRandomOtp($length = 6, $num_only = true)
    {

        if($num_only) {
            $chars = "1234567890";
        } else {
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";//length:36
        }
        $final_rand = '';
        for ($i = 0; $i < $length; $i++) {
            $final_rand .= $chars[rand(0, strlen($chars) - 1)];

        }
        return $final_rand;
    }

    public function smsRetryRemain($suffix) {
        return (Cache::has($this->sms_cache_prefix . $suffix) ? cache($this->sms_cache_prefix . $suffix) : 0) + 1;
    }

    public function increaseSmsRetry($suffix) {
        $counter = $this->smsRetryRemain($suffix);
        if($counter < config('notification.esms.esms_max_send'))
        {
            Cache::put($this->sms_cache_prefix . $suffix, $counter, Carbon::now()->addHours(24));
            return true;
        }
        return false;
    }

    public function registerDevice(Request $request) {
        if($device = Devices::where('did', request('did'))->where('scope', request('scope'))->first()) {
			$device->update(request()->only("os", "type", "did", "device", "scope", "push_token", "user_id"));
			return false;
		}
		if($device = Devices::where('push_token', request('push_token'))->where('scope', request('scope'))->first()) {
			$device->update(request()->only("os", "type", "did", "device", "scope", "push_token", "user_id"));
			return false;
		}
        Devices::create($request->only("os", "type", "device", "did", "scope", "push_token", "user_id"));
        return false;
    }
}