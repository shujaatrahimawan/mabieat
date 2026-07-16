<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Console\ClientCommand;
use Laravel\Passport\Console\InstallCommand;
use Laravel\Passport\Console\KeysCommand;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;


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
         /*ADD THIS LINES*/
        $this->commands([
            InstallCommand::class,
            ClientCommand::class,
            KeysCommand::class,
        ]);

        // Safe mix() alternative: falls back to a plain asset path when the
        // mix-manifest.json entry is missing (e.g. during deployments).
        Blade::directive('mixsafe', function ($expression) {
            return "<?php
                try {
                    echo mix({$expression});
                } catch (\\Exception \$e) {
                    \$path = trim({$expression}, \"'\\\"\");
                    echo asset(\$path);
                }
            ?>";
        });
    }
}
