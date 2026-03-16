<?php

namespace App\Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Core\Traits\HasCompany;
use App\Modules\Core\Models\User;

class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes, HasCompany;
    
    protected $fillable = [
        'company_id',
        'user_id',
        'approved_by',
        'type',
        'start_date',
        'end_date',
        'days_count',
        'reason',
        'status',
        'rejection_reason',
        'attachments',
        'approved_at',
    ];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'attachments' => 'array',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($leave) {
            $leave->days_count = $leave->start_date->diffInDays($leave->end_date) + 1;
        });
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    public function approve($userId)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
        
        // Mettre à jour le solde de congés
        $balance = LeaveBalance::firstOrCreate([
            'company_id' => $this->company_id,
            'user_id' => $this->user_id,
            'year' => $this->start_date->year,
        ], [
            'annual_total' => 25,
        ]);
        
        $field = $this->type . '_used';
        $balance->increment($field, $this->days_count);
        
        return $this;
    }
    
    public function reject($reason)
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
        
        return $this;
    }
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopeForDate($query, $date)
    {
        return $query->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->where('status', 'approved');
    }
}