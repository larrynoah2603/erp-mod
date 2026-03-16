<?php

namespace App\Modules\Core\Traits;

use App\Modules\Core\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasCompany
{
    protected static function bootHasCompany()
    {
        static::creating(function (Model $model) {
            if (auth()->check() && !$model->company_id) {
                $model->company_id = auth()->user()->company_id;
            }
        });
        
        static::addGlobalScope('company', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where($builder->getModel()->getTable() . '.company_id', auth()->user()->company_id);
            }
        });
    }
    
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}