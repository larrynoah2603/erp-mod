<?php

namespace App\Modules\Sales\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Core\Traits\HasCompany;

class Payment extends Model
{
    use HasFactory, SoftDeletes, HasCompany;
    
    protected $fillable = [
        'company_id',
        'invoice_id',
        'payment_number',
        'amount',
        'payment_date',
        'payment_method',
        'reference',
        'notes',
        'status',
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($payment) {
            $payment->payment_number = 'PAY-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        });
    }
    
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}