<?php

namespace App\Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\HasCompany;

class PayrollItem extends Model
{
    use HasFactory, HasCompany;
    
    protected $fillable = [
        'company_id',
        'payroll_id',
        'type',
        'label',
        'amount',
        'quantity',
        'rate',
        'metadata',
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'metadata' => 'array',
    ];
    
    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}