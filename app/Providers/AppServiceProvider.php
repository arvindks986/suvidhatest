<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
		\Validator::extend('mobile', function($attribute, $value, $parameters){
            if(!empty($value) && preg_match('/^[0-9]{10}$/', $value)){
                return true;
            }
            return false;
        });

        \Validator::extend('pin', function($attribute, $value, $parameters){
            if(!empty($value) && preg_match('/^[0-9]{4}$/', $value)){
                return true;
            }
            return false;
        });

        \Validator::extend('password', function($attribute, $value, $parameters){
            if(!empty($value) && preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/', $value)){
                return true;
            }
            return false;
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
