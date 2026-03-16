<?php

namespace App\Modules\HR\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Modules\HR\Models\LeaveRequest;
use App\Modules\HR\Models\LeaveBalance;
use App\Modules\Core\Models\User;

class LeaveManager extends Component
{
    use WithPagination;
    
    public $view = 'calendar'; // calendar or requests
    public $selectedDate;
    public $showForm = false;
    
    // Form fields
    public $type = 'annual';
    public $start_date;
    public $end_date;
    public $reason;
    
    protected $rules = [
        'type' => 'required|in:annual,sick,personal,maternity,other',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after_or_equal:start_date',
        'reason' => 'required|min:10',
    ];
    
    public function mount()
    {
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->addDays(5)->format('Y-m-d');
    }
    
    public function render()
    {
        if ($this->view === 'calendar') {
            return $this->renderCalendar();
        }
        return $this->renderRequests();
    }
    
    private function renderCalendar()
    {
        $leaveRequests = LeaveRequest::with('user')
            ->whereMonth('start_date', now()->month)
            ->where('status', 'approved')
            ->get();
            
        $todayLeave = LeaveRequest::with('user')
            ->forDate(now())
            ->get();
            
        $leaveBalances = null;
        if (!auth()->user()->isAdmin()) {
            $leaveBalances = LeaveBalance::where('user_id', auth()->id())
                ->where('year', now()->year)
                ->first();
        }
        
        return view('hr::livewire.leave-calendar', compact('leaveRequests', 'todayLeave', 'leaveBalances'));
    }
    
    private function renderRequests()
    {
        $requests = LeaveRequest::with('user', 'approver')
            ->when(!auth()->user()->isAdmin(), function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->latest()
            ->paginate(15);
            
        return view('hr::livewire.leave-requests', compact('requests'));
    }
    
    public function submitRequest()
    {
        $this->validate();
        
        LeaveRequest::create([
            'user_id' => auth()->id(),
            'type' => $this->type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'reason' => $this->reason,
            'status' => 'pending',
        ]);
        
        session()->flash('message', 'Demande de congé soumise avec succès.');
        $this->reset(['type', 'start_date', 'end_date', 'reason', 'showForm']);
    }
    
    public function approve($id)
    {
        if (!auth()->user()->isAdmin()) {
            return;
        }
        
        $request = LeaveRequest::find($id);
        $request->approve(auth()->id());
        
        session()->flash('message', 'Demande approuvée.');
    }
    
    public function reject($id, $reason)
    {
        if (!auth()->user()->isAdmin()) {
            return;
        }
        
        $request = LeaveRequest::find($id);
        $request->reject($reason);
        
        session()->flash('message', 'Demande rejetée.');
    }
    
    public function cancel($id)
    {
        $request = LeaveRequest::find($id);
        
        if ($request->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return;
        }
        
        if ($request->status === 'pending') {
            $request->update(['status' => 'cancelled']);
            session()->flash('message', 'Demande annulée.');
        }
    }
    
    public function getPendingCountProperty()
    {
        if (auth()->user()->isAdmin()) {
            return LeaveRequest::pending()->count();
        }
        return 0;
    }
}