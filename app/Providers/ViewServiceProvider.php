<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::addNamespace('core', resource_path('views/modules/core'));
        View::addNamespace('stock', resource_path('views/modules/stock'));
        View::addNamespace('sales', resource_path('views/modules/sales'));
        View::addNamespace('hr', resource_path('views/modules/hr'));
    }

    public function register()
    {
        //
    }
}