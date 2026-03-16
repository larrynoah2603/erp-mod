<?php

namespace App\Modules\HR\Http\Livewire;

use Livewire\Component;
use App\Modules\HR\Models\Attendance;
use App\Modules\HR\Models\LeaveRequest;
use App\Modules\Core\Models\User;
use Illuminate\Support\Facades\DB;

class RHDashboard extends Component
{
    public $selectedMonth;
    public $attendanceData = [];
    public $departmentData = [];
    
    public function mount()
    {
        $this->selectedMonth = now()->format('Y-m');
        $this->loadCharts();
    }
    
    public function render()
    {
        $stats = $this->calculateStats();
        
        return view('hr::livewire.rh-dashboard', compact('stats'));
    }
    
    private function calculateStats()
    {
        $totalEmployees = User::where('is_active', true)->count();
        
        // Taux d'absentéisme du mois
        $workingDays = $this->getWorkingDaysCount();
        $totalExpectedPresences = $totalEmployees * $workingDays;
        
        $absences = LeaveRequest::whereMonth('start_date', now()->month)
            ->where('status', 'approved')
            ->sum('days_count');
            
        $absenteeismRate = $totalExpectedPresences > 0 
            ? round(($absences / $totalExpectedPresences) * 100, 2) 
            : 0;
            
        // Retards du mois
        $lates = Attendance::whereMonth('check_in', now()->month)
            ->where('status', 'late')
            ->count();
            
        // Présence moyenne
        $avgAttendance = Attendance::whereMonth('check_in', now()->month)
            ->select(DB::raw('COUNT(DISTINCT DATE(check_in)) as days_present'))
            ->groupBy('user_id')
            ->get()
            ->avg('days_present');
            
        // Statistiques par département
        $departmentStats = User::where('is_active', true)
            ->select('department', DB::raw('count(*) as total'))
            ->groupBy('department')
            ->get();
            
        $this->departmentData = [
            'labels' => $departmentStats->pluck('department')->map(function($d) { return $d ?? 'Non défini'; }),
            'data' => $departmentStats->pluck('total'),
        ];
        
        // Heatmap des retards par jour de la semaine
        $lateByDay = Attendance::whereMonth('check_in', now()->month)
            ->where('status', 'late')
            ->select(DB::raw('DAYOFWEEK(check_in) as day'), DB::raw('count(*) as total'))
            ->groupBy('day')
            ->get()
            ->keyBy('day');
            
        $days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $lateData = [];
        foreach (range(1, 7) as $day) {
            $lateData[] = $lateByDay[$day]->total ?? 0;
        }
        
        $this->attendanceData = [
            'labels' => $days,
            'data' => $lateData,
        ];
        
        return [
            'total_employees' => $totalEmployees,
            'absenteeism_rate' => $absenteeismRate,
            'total_absences' => $absences,
            'total_lates' => $lates,
            'avg_attendance' => round($avgAttendance ?? 0, 1),
            'working_days' => $workingDays,
        ];
    }
    
    private function getWorkingDaysCount()
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();
        
        $workingDays = 0;
        $current = $start->copy();
        
        while ($current <= $end) {
            if ($current->dayOfWeek !== 0 && $current->dayOfWeek !== 6) { // Pas dimanche (0) ni samedi (6)
                $workingDays++;
            }
            $current->addDay();
        }
        
        return $workingDays;
    }
    
    public function updatedSelectedMonth()
    {
        $this->loadCharts();
        $this->calculateStats();
    }
    
    private function loadCharts()
    {
        // Cette méthode recharge les données du graphique
        $this->dispatchBrowserEvent('refresh-charts', [
            'attendance' => $this->attendanceData,
            'department' => $this->departmentData,
        ]);
    }
}