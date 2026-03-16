<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Modules\Core\Traits\HasCompany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasCompany, SoftDeletes;
    
    protected $fillable = [
        'company_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'birth_date',
        'hire_date',
        'job_title',
        'department',
        'employee_id',
        'salary',
        'contract_type',
        'contract_settings',
        'is_active',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'hire_date' => 'date',
        'is_active' => 'boolean',
        'contract_settings' => 'array',
        'salary' => 'decimal:2',
    ];
    
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
    
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }
    
    public function isManager()
    {
        return $this->hasRole('manager');
    }
}