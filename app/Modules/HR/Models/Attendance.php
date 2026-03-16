<?php

namespace App\Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\HasCompany;
use App\Modules\Core\Models\User;

class Attendance extends Model
{
    use HasFactory, HasCompany;
    
    protected $fillable = [
        'company_id',
        'user_id',
        'check_in',
        'check_out',
        'status',
        'ip_address',
        'latitude',
        'longitude',
        'working_hours',
        'metadata',
    ];
    
    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'working_hours' => 'decimal:2',
        'metadata' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function calculateWorkingHours()
    {
        if ($this->check_out) {
            $diffInMinutes = $this->check_in->diffInMinutes($this->check_out);
            $this->working_hours = round($diffInMinutes / 60, 2);
            $this->save();
        }
        return $this->working_hours;
    }
    
    public function scopeToday($query)
    {
        return $query->whereDate('check_in', now());
    }
    
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('check_in', [now()->startOfWeek(), now()->endOfWeek()]);
    }
    
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('check_in', now()->month);
    }
    
    public function scopePresent($query)
    {
        return $query->whereNull('check_out');
    }
    
    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }
}