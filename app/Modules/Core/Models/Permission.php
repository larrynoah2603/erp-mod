<?php

namespace App\Modules\Core\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use App\Modules\Core\Traits\HasCompany;

class Permission extends SpatiePermission
{
    use HasCompany;
    
    protected $fillable = [
        'name',
        'guard_name',
        'module',
        'company_id',
    ];
}