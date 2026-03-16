<?php

namespace App\Modules\Sales\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Core\Traits\HasCompany;

class Customer extends Model
{
    use HasFactory, SoftDeletes, HasCompany;
    
    protected $fillable = [
        'company_id',
        'customer_number',
        'type',
        'name',
        'contact_name',
        'email',
        'phone',
        'mobile',
        'address',
        'city',
        'postal_code',
        'country',
        'siret',
        'vat_number',
        'payment_terms',
        'credit_limit',
        'notes',
        'is_active',
    ];
    
    protected $casts = [
        'credit_limit' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($customer) {
            $customer->customer_number = 'CUST-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        });
    }
    
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'client_name', 'name');
    }
}