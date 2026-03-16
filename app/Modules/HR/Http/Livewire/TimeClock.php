<?php

namespace App\Modules\HR\Http\Livewire;

use Livewire\Component;
use App\Modules\HR\Models\Attendance;
use App\Modules\Core\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;

class TimeClock extends Component
{
    public $currentTime;
    public $currentAttendance;
    public $weekAttendances;
    public $workingHoursToday = 0;
    
    protected $listeners = ['tick' => 'updateTime'];
    
    public function mount()
    {
        $this->currentTime = now()->format('H:i:s');
        $this->loadCurrentAttendance();
        $this->loadWeekAttendances();
    }
    
    public function render()
    {
        return view('hr::livewire.time-clock');
    }
    
    public function updateTime()
    {
        $this->currentTime = now()->format('H:i:s');
    }
    
    public function loadCurrentAttendance()
    {
        $this->currentAttendance = Attendance::where('user_id', auth()->id())
            ->whereDate('check_in', now())
            ->whereNull('check_out')
            ->first();
            
        if ($this->currentAttendance) {
            $this->workingHoursToday = round(now()->diffInMinutes($this->currentAttendance->check_in) / 60, 2);
        }
    }
    
    public function loadWeekAttendances()
    {
        $this->weekAttendances = Attendance::where('user_id', auth()->id())
            ->whereBetween('check_in', [now()->startOfWeek(), now()->endOfWeek()])
            ->orderBy('check_in', 'desc')
            ->get();
    }
    
    public function checkIn()
    {
        $company = auth()->user()->company;
        $workingHours = $company->working_hours;
        $dayOfWeek = strtolower(now()->format('l'));
        
        // Vérifier si l'utilisateur a déjà pointé aujourd'hui
        $existing = Attendance::where('user_id', auth()->id())
            ->whereDate('check_in', now())
            ->first();
            
        if ($existing) {
            session()->flash('error', 'Vous avez déjà pointé aujourd\'hui.');
            return;
        }
        
        // Déterminer le statut (présent ou retard)
        $status = 'present';
        if (isset($workingHours[$dayOfWeek]['start']) && $workingHours[$dayOfWeek]['start']) {
            $startTime = Carbon::parse($workingHours[$dayOfWeek]['start']);
            if (now()->gt($startTime)) {
                $status = 'late';
            }
        }
        
        $attendance = Attendance::create([
            'user_id' => auth()->id(),
            'check_in' => now(),
            'status' => $status,
            'ip_address' => Request::ip(),
            'latitude' => request()->input('latitude'),
            'longitude' => request()->input('longitude'),
            'metadata' => [
                'user_agent' => Request::userAgent(),
                'check_in_method' => 'web'
            ]
        ]);
        
        $this->loadCurrentAttendance();
        $this->loadWeekAttendances();
        
        session()->flash('success', 'Pointage d\'entrée enregistré à ' . now()->format('H:i'));
    }
    
    public function checkOut()
    {
        if (!$this->currentAttendance) {
            session()->flash('error', 'Aucun pointage d\'entrée trouvé.');
            return;
        }
        
        $this->currentAttendance->update([
            'check_out' => now()
        ]);
        
        $this->currentAttendance->calculateWorkingHours();
        
        $this->loadCurrentAttendance();
        $this->loadWeekAttendances();
        
        session()->flash('success', 'Pointage de sortie enregistré à ' . now()->format('H:i'));
    }
    
    public function getLocation()
    {
        $this->dispatchBrowserEvent('get-location');
    }
}