<?php

namespace App\Modules\Stock\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\HasCompany;
use App\Modules\Core\Models\User;

class StockMovement extends Model
{
    use HasFactory, HasCompany;
    
    protected $fillable = [
        'company_id',
        'product_id',
        'user_id',
        'type',
        'quantity',
        'before_quantity',
        'after_quantity',
        'reason',
        'reference_type',
        'reference_id',
        'metadata',
    ];
    
    protected $casts = [
        'metadata' => 'array',
    ];
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function reference()
    {
        return $this->morphTo();
    }
}