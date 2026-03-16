<?php

namespace App\Modules\Stock\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Core\Traits\HasCompany;

class Supplier extends Model
{
    use HasFactory, SoftDeletes, HasCompany;
    
    protected $fillable = [
        'company_id',
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
        'delivery_delay',
        'notes',
        'is_active',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}