<?php

namespace App\Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Core\Traits\HasCompany;
use App\Modules\Core\Models\User;

class SalaryConfig extends Model
{
    use HasFactory, SoftDeletes, HasCompany;
    
    protected $table = 'salary_configs';
    
    protected $fillable = [
        'company_id',
        'user_id',
        'base_salary',
        'hourly_rate',
        'pay_type',
        'overtime_rate',
        'night_rate',
        'holiday_rate',
        'bonuses',
        'deductions',
        'social_charges',
        'effective_from',
        'effective_to',
        'is_active',
    ];
    
    protected $casts = [
        'base_salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'night_rate' => 'decimal:2',
        'holiday_rate' => 'decimal:2',
        'bonuses' => 'array',
        'deductions' => 'array',
        'social_charges' => 'array',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}