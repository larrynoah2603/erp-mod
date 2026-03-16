<?php

namespace App\Modules\Core\Http\Livewire;

use Livewire\Component;
use App\Modules\Sales\Models\Invoice;
use App\Modules\Stock\Models\Product;
use App\Modules\HR\Models\Attendance;
use App\Modules\Core\Models\User;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $dateRange = 'month';
    public $chartData = [];
    
    public function mount()
    {
        $this->loadChartData();
    }
    
    public function render()
    {
        $stats = [
            'total_sales' => Invoice::where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->sum('total'),
            'sales_count' => Invoice::where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->count(),
            'low_stock_count' => Product::lowStock()->count(),
            'out_of_stock_count' => Product::outOfStock()->count(),
            'active_employees' => User::where('is_active', true)->count(),
            'present_today' => Attendance::whereDate('check_in', now())
                ->whereNull('check_out')
                ->count(),
            'pending_invoices' => Invoice::where('status', 'sent')->count(),
            'overdue_invoices' => Invoice::overdue()->count(),
        ];
        
        $recentInvoices = Invoice::with('user')
            ->latest()
            ->take(5)
            ->get();
            
        $lowStockProducts = Product::lowStock()
            ->take(5)
            ->get();
            
        $departmentStats = User::where('is_active', true)
            ->select('department', DB::raw('count(*) as total'))
            ->groupBy('department')
            ->get();
        
        return view('core::livewire.dashboard', compact(
            'stats', 
            'recentInvoices', 
            'lowStockProducts',
            'departmentStats'
        ));
    }
    
    public function loadChartData()
    {
        $months = collect();
        $salesData = collect();
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push($date->format('M Y'));
            
            $total = Invoice::where('status', 'paid')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('total');
                
            $salesData->push($total);
        }
        
        $this->chartData = [
            'labels' => $months,
            'data' => $salesData,
        ];
    }
    
    public function updatedDateRange()
    {
        $this->loadChartData();
    }
}