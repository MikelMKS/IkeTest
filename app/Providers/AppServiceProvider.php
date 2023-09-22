<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('true_if', function ($attribute, $value, $parameters, $validator) {
            // return $value === true || $value === 'true' || $value === 1 || $value === '1';
            return $value === true;
        });

        Validator::extend('unique_user', function ($attribute, $value, $parameters, $validator) {
            $users = User::get()->map(function ($item) {
                $item->user = Crypt::decrypt($item->user);
                return $item;
            });

            $users = $users->filter(function ($item) use ($value) {
                return $item->user === $value;
            });

            return $users->isEmpty();
        });
    }
}
