<?php

namespace App\Modules\Stock\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Core\Traits\HasCompany;

class Category extends Model
{
    use HasFactory, SoftDeletes, HasCompany;
    
    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'description',
        'parent_id',
        'image_path',
        'is_active',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}