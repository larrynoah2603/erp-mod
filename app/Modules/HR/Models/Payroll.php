<?php

namespace App\Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Core\Traits\HasCompany;
use App\Modules\Core\Models\User;

class Payroll extends Model
{
    use HasFactory, SoftDeletes, HasCompany;
    
    protected $fillable = [
        'company_id',
        'user_id',
        'payroll_number',
        'period_start',
        'period_end',
        'payment_date',
        'base_salary',
        'overtime_hours',
        'overtime_amount',
        'bonuses',
        'deductions',
        'social_charges',
        'net_salary',
        'gross_salary',
        'details',
        'status',
        'paid_at',
        'notes',
    ];
    
    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'payment_date' => 'date',
        'paid_at' => 'datetime',
        'base_salary' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'deductions' => 'decimal:2',
        'social_charges' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'details' => 'array',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function items()
    {
        return $this->hasMany(PayrollItem::class);
    }
}s