protected $routeMiddleware = [
    // ... autres middleware
    'company.scope' => \App\Modules\Core\Http\Middleware\CompanyScope::class,
];

protected $middlewareGroups = [
    'web' => [
        // ... autres middleware
        \App\Modules\Core\Http\Middleware\CompanyScope::class,
    ],
];