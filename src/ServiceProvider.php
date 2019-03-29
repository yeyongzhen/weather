<?php
/**
 * Created by PhpStorm.
 * User: Yongzhen Ye
 * Date: 2019/3/29
 * Time: 15:31
 */
namespace Yyz\Weather;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(Weather::class, function(){
            return new Weather(config('services.weather.key'));
        });

        $this->app->alias(Weather::class, 'weather');
    }

    public function provides()
    {
        return [Weather::class, 'weather'];
    }
}