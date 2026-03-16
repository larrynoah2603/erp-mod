<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\HR\Models\Payroll;
use App\Modules\HR\Models\Attendance;
use App\Modules\HR\Models\SalaryConfig;
use App\Modules\Core\Models\User;
use Carbon\Carbon;

class GeneratePayslips extends Command
{
    protected $signature = 'hr:generate-payslips {month?} {year?}';
    protected $description = 'Generate payslips for all employees';

    public function handle()
    {
        $month = $this->argument('month') ?? now()->month;
        $year = $this->argument('year') ?? now()->year;
        
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        $this->info("Génération des fiches de paie pour {$startDate->format('F Y')}");
        
        $users = User::where('is_active', true)->get();
        $bar = $this->output->createProgressBar($users->count());
        
        foreach ($users as $user) {
            $this->generatePayslip($user, $startDate, $endDate);
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Génération terminée!');
    }
    
    private function generatePayslip($user, $startDate, $endDate)
    {
        // Récupérer la config salariale
        $salaryConfig = SalaryConfig::where('user_id', $user->id)
            ->where('effective_from', '<=', $startDate)
            ->where(function($q) use ($endDate) {
                $q->where('effective_to', '>=', $endDate)
                  ->orWhereNull('effective_to');
            })
            ->first();
            
        if (!$salaryConfig) {
            $this->warn("Pas de configuration salariale pour {$user->full_name}");
            return;
        }
        
        // Récupérer les pointages
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('check_in', [$startDate, $endDate])
            ->get();
            
        // Calculer les heures
        $totalHours = $attendances->sum('working_hours');
        $overtimeHours = max(0, $totalHours - 151.67); // 35h/semaine
        
        // Calculer les montants
        $baseSalary = $salaryConfig->base_salary;
        $overtimeAmount = $overtimeHours * ($salaryConfig->hourly_rate * $salaryConfig->overtime_rate);
        
        // Créer la fiche de paie
        $payroll = Payroll::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'payroll_number' => 'PAIE-' . $startDate->format('Ym') . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
            'period_start' => $startDate,
            'period_end' => $endDate,
            'payment_date' => $endDate->copy()->addDays(5),
            'base_salary' => $baseSalary,
            'overtime_hours' => $overtimeHours,
            'overtime_amount' => $overtimeAmount,
            'bonuses' => 0,
            'deductions' => 0,
            'social_charges' => $baseSalary * 0.45, // 45% de charges
            'gross_salary' => $baseSalary + $overtimeAmount,
            'net_salary' => ($baseSalary + $overtimeAmount) * 0.75, // 25% de prélèvements
            'status' => 'calculated',
            'details' => [
                'total_hours' => $totalHours,
                'regular_hours' => 151.67,
                'overtime_hours' => $overtimeHours,
                'attendance_days' => $attendances->count(),
            ],
        ]);
        
        return $payroll;
    }
}