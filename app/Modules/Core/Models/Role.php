<?php

namespace App\Modules\Core\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use App\Modules\Core\Traits\HasCompany;

class Role extends SpatieRole
{
    use HasCompany;
    
    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
        'company_id',
    ];
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'model_has_roles', 'role_id', 'model_id')
            ->where('model_type', User::class);
    }
}