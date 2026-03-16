<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Stock\Models\Product;
use App\Modules\Core\Models\Company;
use App\Modules\Core\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StockAlertNotification;

class StockAlert extends Command
{
    protected $signature = 'stock:check-alerts';
    protected $description = 'Check for low stock products and send alerts';

    public function handle()
    {
        $this->info('Vérification des stocks bas...');
        
        $companies = Company::where('is_active', true)->get();
        
        foreach ($companies as $company) {
            $lowStockProducts = Product::where('company_id', $company->id)
                ->lowStock()
                ->get();
                
            if ($lowStockProducts->count() > 0) {
                $this->info("{$company->name}: {$lowStockProducts->count()} produits en stock bas");
                
                // Notifier les managers
                $managers = User::role('manager')
                    ->where('company_id', $company->id)
                    ->get();
                    
                Notification::send($managers, new StockAlertNotification($lowStockProducts));
            }
        }
        
        $this->info('Vérification terminée!');
    }
}