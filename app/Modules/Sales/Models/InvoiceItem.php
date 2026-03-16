<?php

namespace App\Modules\Sales\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\HasCompany;
use App\Modules\Stock\Models\Product;

class InvoiceItem extends Model
{
    use HasFactory, HasCompany;
    
    protected $fillable = [
        'company_id',
        'invoice_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'discount',
        'tax_rate',
        'subtotal',
        'total',
    ];
    
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($item) {
            $item->calculateTotals();
        });
        
        static::updating(function ($item) {
            $item->calculateTotals();
        });
    }
    
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function calculateTotals()
    {
        $subtotal = ($this->unit_price * $this->quantity) - $this->discount;
        $this->subtotal = $subtotal;
        $this->total = $subtotal * (1 + ($this->tax_rate / 100));
        
        return $this;
    }
    
    public function getTaxAmountAttribute()
    {
        return $this->total - $this->subtotal;
    }
}