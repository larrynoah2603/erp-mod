<?php

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\ModuleServiceProvider;
use App\Providers\ViewServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    ModuleServiceProvider::class,
    ViewServiceProvider::class,
];
