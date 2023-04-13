<?php

namespace Weelis\Notification\Controller;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Weelis\Notification\Model\Devices;
use Illuminate\Foundation\Validation\ValidatesRequests;

class DevicesController extends Controller
{
    use ValidatesRequests;
	public function registerDevice()
	{
		$this->validate(request(), [
			'os'         => 'required',
			// 'device'       => 'required',
			'type'       => 'required',
			'did'        => 'required',
			'scope'      => 'required',
//			'push_token' => 'required'
		]);
		if ($user = request()->user()) {
			request()->merge(["user_id" => $user->id]);
		} else {
			request()->merge(["user_id" => null]);
		}
		if($device = Devices::where('did', request('did'))->where('scope', request('scope'))->first()) {
			$device->update(request()->only("os", "type", "did", "device", "scope", "push_token", "user_id"));
			return $this->handleSuccess(__('notification::device.success'));
		}
		if($device = Devices::where('push_token', request('push_token'))->where('scope', request('scope'))->first()) {
			$device->update(request()->only("os", "type", "did", "device", "scope", "push_token", "user_id"));
			return $this->handleSuccess(__('notification::device.success'));
		}
		Devices::create(request()->only("os", "type", "did", "device", "scope", "push_token", "user_id"));
		return $this->handleSuccess(__('notification::device.success'));
	}

	public function unregisterDevice()
	{
        $this->validate(request(), [
			'did'        => 'required',
			'scope'      => 'required'
        ]);
        Devices::where('did', '=', request("did"))->where('scope', request("scope"))->delete();

		return $this->handleSuccess(__('notification::device.destroy'));
	}

	public function handleError($code, $message)
    {
        return response(json_encode([
            'status'  => 0,
            'message' => $message
        ]), $code)->header('Content-Type', 'application/json');
    }

    public function handleSuccess($message)
    {
        return [
            'status'  => 1,
            'message' => $message
        ];
    }
}
