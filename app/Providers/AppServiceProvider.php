<?php

namespace App\Providers;

use App\Modules\Core\Models\User as CoreUser;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Throwable;

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
        $this->forceCoreUserModelForAuth();
        $this->guardSessionDriverAgainstMissingTable();
    }

    private function forceCoreUserModelForAuth(): void
    {
        Config::set('auth.providers.users.model', CoreUser::class);
    }

    private function guardSessionDriverAgainstMissingTable(): void
    {
        if (Config::get('session.driver') !== 'database') {
            return;
        }

        try {
            if (! Schema::hasTable('sessions')) {
                Config::set('session.driver', 'file');
            }
        } catch (Throwable) {
            // If DB is unavailable during bootstrap, avoid hard-failing user requests.
            Config::set('session.driver', 'file');
        }
    }
}
