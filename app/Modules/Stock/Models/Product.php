<?php

namespace App\Modules\Stock\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Core\Traits\HasCompany;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasCompany;
    
    protected $fillable = [
        'company_id',
        'sku',
        'barcode',
        'name',
        'slug',
        'description',
        'price',
        'cost',
        'quantity',
        'min_quantity',
        'max_quantity',
        'unit',
        'category',
        'supplier',
        'location',
        'attributes',
        'is_active',
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'attributes' => 'array',
        'is_active' => 'boolean',
    ];
    
    protected $appends = ['profit_margin', 'is_low_stock'];
    
    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }
    
    public function getProfitMarginAttribute()
    {
        if ($this->cost && $this->price > 0) {
            return round((($this->price - $this->cost) / $this->price) * 100, 2);
        }
        return 0;
    }
    
    public function getIsLowStockAttribute()
    {
        return $this->quantity <= $this->min_quantity;
    }
    
    public function adjustStock($quantity, $type, $reason = null, $reference = null)
    {
        $before = $this->quantity;
        
        switch ($type) {
            case 'in':
                $this->quantity += $quantity;
                break;
            case 'out':
                $this->quantity -= $quantity;
                break;
            case 'adjustment':
                $this->quantity = $quantity;
                break;
        }
        
        $this->save();
        
        // Enregistrer le mouvement
        $this->movements()->create([
            'company_id' => $this->company_id,
            'user_id' => auth()->id(),
            'type' => $type,
            'quantity' => $quantity,
            'before_quantity' => $before,
            'after_quantity' => $this->quantity,
            'reason' => $reason,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference ? $reference->id : null,
        ]);
        
        return $this;
    }
    
    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= min_quantity');
    }
    
    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', '<=', 0);
    }
    
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}