# laravel-weelis-notification
Weelis Notification Channel (APN, FCM, ESMS)

Use this package to send push notifications via Laravel to Firebase Cloud Messaging, APN, ESMS. Laravel 5.3+ required.

## Install

This package can be installed through Composer.

``` bash
composer require weelis/notification
```

Once installed, add the service provider:

```php
// config/app.php
'providers' => [
    ...
    Weelis\Notification\NotificationServiceProvider::class,
    ...
];
'aliases' => [
    ...
    'NotificationHelper'=> \Weelis\Notification\Facade\NotificationHelper::class
    ...
]
```

Publish the config file:

``` bash
php artisan vendor:publish --provider="Weelis\Notification\NotificationServiceProvider"
```
### Register device

Using existing controller add this to your route
```php
Route::group(['prefix' => 'device'], function () {
    Route::post('register', '\Weelis\Notification\Controller\DevicesController@registerDevice');
    Route::post('unregister', '\Weelis\Notification\Controller\DevicesController@unregisterDevice');
});
```
### Using notification database model & report

```php
use Weelis\Notification\Base\Notifiable;
use Weelis\Notification\Model\NotificationModel;

class User extends Authenticatable
{
    use Notifiable, NotificationModel;
    ...
}
```

Using facade
```php
$request => ['os'         => 'required',
//          'device'       => 'required',
			'type'       => 'required',
			'did'        => 'required',
			'scope'      => 'required',
//			'push_token' => 'required']
NotificationHelper::registerDevice($request);
```

### Setting up the FCM service

The following config file will be published in `config/notification.php`. Add your Firebase API Key here.

Set up .env file
```php
FCM_API_KEY=legacy key
```
OR

```php
return [
    /*
     * Add the Firebase API key
     */
    'fcm' => [
        'api_key' => ''
    ],
];
```

#### Example Usage

Use Artisan to create a notification:

```bash
php artisan make:notification SomeNotification
```

Return `[fcm]` in the `public function via($notifiable)` method of your notification:

```php
public function via($notifiable)
{
    return ['fcm'];
}
```

Add the method `public function toFcm($notifiable)` to your notification, and return an instance of `FcmMessage`: 

```php
public function toFcm($notifiable) 
{
    $message = new Weelis\Notification\Fcm\FcmMessage();
    $message->content([
        'title'        => 'Foo', 
        'body'         => 'Bar', 
        'sound'        => '', // Optional 
        'icon'         => '', // Optional
        'click_action' => '' // Optional
    ])->data([
        'param1' => 'baz' // Optional
    ])->priority(Weelis\Notification\Fcm\FcmMessage::PRIORITY_HIGH); // Optional - Default is 'normal'.
    
    return $message;
}
```

When sending to specific device, make sure your notifiable entity has `routeNotificationForFcm` method defined: 

```php
/**
 * Route notifications for the FCM channel.
 *
 * @return string
 */
public function routeNotificationForFcm()
{
    return $this->device_token;
}
```

When sending to a topic, you may define so within the `toFcm` method in the notification:

```php
public function toFcm($notifiable) 
{
    $message = new Weelis\Notification\Fcm\FcmMessage();
    $message->to('the-topic', $recipientIsTopic = true)
    ->content([...])
    ->data([...]);
    
    return $message;
}
```

### Setting up the APN service

Before using the APN Service, follow the [Provisioning and Development guide from Apple](https://developer.apple.com/library/ios/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/Chapters/ProvisioningDevelopment.html)

You will need to generate a certificate for you application, before you can use this channel. Configure the path in config/broadcasting.php

Set up .env file
```php
APN_KEY_DEV={"user":{"cert":"/storage/path/file","pass":"passphrase"},"worker":{"cert":"/storage/path/file","pass":"passphrase"}}
APN_KEY_PRO={"user":{"cert":"/storage/path/file","pass":"passphrase"},"worker":{"cert":"/storage/path/file","pass":"passphrase"}}
```

#### Usage

You can now send messages to APN by creating a ApnMessage:

Return `[apn]` in the `public function via($notifiable)` method of your notification:

```php
use Weelis\Notification\Apn\ApnMessage;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return ['apn'];
    }

    public function toApn($notifiable)
    {
        return ApnMessage::create()
            ->badge(1)
            ->title('Account approved')
            ->body("Your {$notifiable->service} account was approved!");
    }
}
```

In your notifiable model, make sure to include a routeNotificationForApn() method, which return one or an array of tokens. 

```php
public function routeNotificationForApn()
{
    return $this->apn_token;
}
```

### Setting up the ESMS service
Set up .env file
```php
ESMS_API_KEY=<key>
ESMS_SECRET_KEY=<secrect>
ESMS_SMS_TYPE=6
ESMS_BRAND_NAME=
ESMS_URL=http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_get
ESMS_DAY_MAX=5
```

#### Usage

You can now send messages to Esms:

Return `[esms]` in the `public function via($notifiable)` method of your notification:

```php
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return ['esms'];
    }

    public function toApn($notifiable)
    {
        return [
            "sms" => <your sms message>
        ];
    }
}
```

When sending to specific device, make sure your notifiable entity has `routeNotificationForEsms` method defined: 

```php
/**
 * Route notifications for the FCM channel.
 *
 * @return string
 */
public function routeNotificationForEsms()
{
    return $this->phone;
}
```

### Sending user channel

```php
use Weelis\Notification\Base\NotificationToUser;

$user->notify(new NotificationToUser([
    'scope' => "user",
    'types' => ['email', 'esms', 'apn', 'fcm'],
    'title' => "Foo",
    'body' => "Bar",
    'icon' => "", // Optional
    'sound' => "", // Optional
    'type' => "Foo Bar",
    'type_slug' => "foo-bar",
    'email_view' => 'foo.bar', // Optional
    'custom' => [ // use for email view param also
        'foo' => '',
        'bar' => '',
        'click_action' => ""
    ] // Optional
]), $notifitable_model  // Optional);
```


## License

This project belong to vias company.
