<?php

namespace Weelis\Notification;

use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;
use Weelis\Notification\Fcm\FcmChannel;
use Weelis\Notification\Apn\ApnChannel;
use Weelis\Notification\Esms\EsmsChannel;
use Weelis\Notification\Db\DatabaseChannel;
use Weelis\Notification\Classes\NotificationUtil;

/**
 * Class FcmNotificationServiceProvider
 * @package Weelis\Notification
 */
class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     * 
     * @return void
     */
    public function boot()
    {
	    $this->loadTranslationsFrom(__DIR__ . '/lang', 'notification');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->publishes([
            __DIR__.'/config/config.php' => config_path('notification.php'),
        ]);
    }

    /**
     * Register
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/config.php', 'notification'
        );

        $app = $this->app;
        $this->app->make(ChannelManager::class)->extend('fcm', function() use ($app) {
            return $app->make(FcmChannel::class);
        });
        $this->app->make(ChannelManager::class)->extend('apn', function() use ($app) {
            return $app->make(ApnChannel::class);
        });
        $this->app->make(ChannelManager::class)->extend('esms', function() use ($app) {
            return $app->make(EsmsChannel::class);
        });
        $this->app->make(ChannelManager::class)->extend('db', function() use ($app) {
            return $app->make(DatabaseChannel::class);
        });
        $this->app->singleton("notification.helper", function($app){
            return new NotificationUtil();
        });
    }
}