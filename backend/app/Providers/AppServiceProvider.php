<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
        // Set the default timezone
        date_default_timezone_set('Asia/Kolkata');
        
        // Set Carbon's default timezone
        Carbon::setLocale('en');
        Carbon::setToStringFormat('Y-m-d H:i:s');
        Carbon::setTestNow(Carbon::now('Asia/Kolkata'));
        Inertia::share([
            'auth.user' => function (Request $request) {
                return $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'roles' => $request->user()->getRoleNames(),
                ] : null;
            },
            'flash' => function () {
                return [
                    'success' => session('success'),
                    'error' => session('error'),
                ];
            },
        ]);
    }
}
