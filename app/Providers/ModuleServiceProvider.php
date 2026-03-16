<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerModules();
    }

    public function boot()
    {
        $this->loadModuleMigrations();
        $this->loadModuleViews();
        $this->loadModuleRoutes();
    }

    private function registerModules()
    {
        $modules = ['Core', 'Sales', 'Stock', 'HR'];
        
        foreach ($modules as $module) {
            // Register module service providers if they exist
            $providerClass = "App\\Modules\\{$module}\\{$module}ServiceProvider";
            if (class_exists($providerClass)) {
                $this->app->register($providerClass);
            }
        }
    }

    private function loadModuleMigrations()
    {
        $modules = ['Core', 'Sales', 'Stock', 'HR'];
        
        foreach ($modules as $module) {
            $migrationPath = app_path("Modules/{$module}/Database/Migrations");
            if (is_dir($migrationPath)) {
                $this->loadMigrationsFrom($migrationPath);
            }
        }
    }

    private function loadModuleViews()
    {
        $modules = ['Core', 'Sales', 'Stock', 'HR'];
        
        foreach ($modules as $module) {
            $viewPath = resource_path("views/modules/" . strtolower($module));
            if (is_dir($viewPath)) {
                $this->loadViewsFrom($viewPath, strtolower($module));
            }
        }
    }

    private function loadModuleRoutes()
    {
        $modules = ['Core', 'Sales', 'Stock', 'HR'];
        
        foreach ($modules as $module) {
            $routesPath = base_path("routes/modules/" . strtolower($module) . ".php");
            if (file_exists($routesPath)) {
                Route::middleware('web')
                    ->namespace("App\\Modules\\{$module}\\Http\\Controllers")
                    ->group($routesPath);
            }
        }
    }
}