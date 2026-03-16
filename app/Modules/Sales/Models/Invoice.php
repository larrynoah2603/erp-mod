<?php

namespace App\Modules\Sales\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Core\Traits\HasCompany;
use App\Modules\Core\Models\User;
use App\Modules\Stock\Models\Product;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, HasCompany;
    
    protected $fillable = [
        'company_id',
        'user_id',
        'invoice_number',
        'client_name',
        'client_email',
        'client_phone',
        'client_address',
        'client_vat',
        'issue_date',
        'due_date',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total',
        'status',
        'notes',
        'payment_details',
        'paid_at',
    ];
    
    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'payment_details' => 'array',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($invoice) {
            $invoice->invoice_number = self::generateInvoiceNumber();
        });
    }
    
    public static function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        $lastInvoice = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->latest()
            ->first();
            
        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice->invoice_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "FAC-{$year}{$month}-{$newNumber}";
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
    
    public function calculateTotals()
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->tax_amount = $this->items->sum('tax_amount');
        $this->total = $this->subtotal + $this->tax_amount;
        
        return $this;
    }
    
    public function markAsPaid($paymentDetails = null)
    {
        $this->status = 'paid';
        $this->paid_at = now();
        $this->payment_details = $paymentDetails;
        $this->save();
        
        // Mettre à jour le stock si nécessaire
        foreach ($this->items as $item) {
            if ($item->product_id) {
                $product = Product::find($item->product_id);
                $product->adjustStock($item->quantity, 'out', 'Vente facture ' . $this->invoice_number, $this);
            }
        }
    }
    
    public function isOverdue()
    {
        return $this->status === 'sent' && $this->due_date->isPast();
    }
    
    public function scopeOverdue($query)
    {
        return $query->where('status', 'sent')
            ->where('due_date', '<', now());
    }
    
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    
    public function scopeForPeriod($query, $start, $end)
    {
        return $query->whereBetween('issue_date', [$start, $end]);
    }
}