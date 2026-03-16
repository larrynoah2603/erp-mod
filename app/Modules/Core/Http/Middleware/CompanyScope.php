<?php

namespace App\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $companyId = Auth::user()->company_id;
            
            // Partage la company_id avec toutes les vues
            view()->share('currentCompanyId', $companyId);
            
            // Ajoute un scope global pour tous les modèles
            $this->applyCompanyScope($companyId);
        }
        
        return $next($request);
    }
    
    private function applyCompanyScope($companyId)
    {
        // Liste des modèles qui doivent être scopés par company
        $models = [
            \App\Modules\Core\Models\User::class,
            \App\Modules\Sales\Models\Customer::class,
            \App\Modules\Stock\Models\Product::class,
            \App\Modules\HR\Models\Attendance::class,
            // Ajoutez tous vos modèles ici
        ];
        
        foreach ($models as $model) {
            if (class_exists($model)) {
                $model::addGlobalScope('company', function (Builder $builder) use ($companyId) {
                    $builder->where('company_id', $companyId);
                });
            }
        }
    }
}