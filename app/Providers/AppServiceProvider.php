<?php

namespace App\Providers;

use App\Http\Resources\ShiftDayCollection;
use App\Http\Resources\ShiftDayResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ShiftDayCollection::withoutWrapping();
        ShiftDayResource::withoutWrapping();
    }
}
