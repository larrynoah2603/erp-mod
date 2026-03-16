<?php

namespace App\Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Core\Traits\HasCompany;
use App\Modules\Core\Models\User;

class Department extends Model
{
    use HasFactory, SoftDeletes, HasCompany;
    
    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'manager_id',
        'parent_id',
        'budget',
        'is_active',
    ];
    
    protected $casts = [
        'budget' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
    
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }
    
    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }
    
    public function users()
    {
        return $this->hasMany(User::class, 'department', 'name');
    }
}