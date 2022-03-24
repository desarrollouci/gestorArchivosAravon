<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

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
        Schema::defaultStringLength(191);

        Carbon::setLocale(config('app.locale'));
        Carbon::setUTF8(true);
        
        //setlocale(LC_ALL, 'es_ES', 'es', 'ES', 'es_ES.utf8');
        setlocale(LC_NUMERIC, 'en_EN', 'en', 'EN', 'en_EN.utf8');
        setlocale(LC_COLLATE, 'es_ES', 'es', 'ES', 'es_ES.utf8');
        setlocale(LC_CTYPE, 'es_ES', 'es', 'ES', 'es_ES.utf8');
        setlocale(LC_TIME, 'es_ES', 'es', 'ES', 'es_ES.utf8');
    }
}
