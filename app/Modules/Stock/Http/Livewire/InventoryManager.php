<?php

namespace App\Modules\Stock\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Modules\Stock\Models\Product;
use App\Modules\Stock\Models\StockMovement;

class InventoryManager extends Component
{
    use WithPagination;
    
    public $search = '';
    public $categoryFilter = '';
    public $stockStatus = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    
    public $showForm = false;
    public $editingProduct = null;
    
    public $sku;
    public $name;
    public $description;
    public $price;
    public $cost;
    public $quantity;
    public $min_quantity;
    public $category;
    public $supplier;
    public $location;
    
    protected $rules = [
        'sku' => 'required|unique:products,sku',
        'name' => 'required|min:3',
        'price' => 'required|numeric|min:0',
        'cost' => 'nullable|numeric|min:0',
        'quantity' => 'required|integer|min:0',
        'min_quantity' => 'required|integer|min:0',
        'category' => 'nullable',
        'supplier' => 'nullable',
    ];
    
    public function render()
    {
        $products = Product::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%')
                      ->orWhere('barcode', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category', $this->categoryFilter);
            })
            ->when($this->stockStatus === 'low', function ($query) {
                $query->lowStock();
            })
            ->when($this->stockStatus === 'out', function ($query) {
                $query->outOfStock();
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);
            
        $categories = Product::distinct('category')->pluck('category');
        $lowStockCount = Product::lowStock()->count();
        $outOfStockCount = Product::outOfStock()->count();
        $totalProducts = Product::count();
        
        return view('stock::livewire.inventory-manager', compact(
            'products', 
            'categories', 
            'lowStockCount', 
            'outOfStockCount',
            'totalProducts'
        ));
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
    
    public function create()
    {
        $this->resetValidation();
        $this->reset(['sku', 'name', 'description', 'price', 'cost', 'quantity', 'min_quantity', 'category', 'supplier', 'location']);
        $this->showForm = true;
        $this->editingProduct = null;
    }
    
    public function edit($id)
    {
        $this->editingProduct = Product::find($id);
        $this->sku = $this->editingProduct->sku;
        $this->name = $this->editingProduct->name;
        $this->description = $this->editingProduct->description;
        $this->price = $this->editingProduct->price;
        $this->cost = $this->editingProduct->cost;
        $this->quantity = $this->editingProduct->quantity;
        $this->min_quantity = $this->editingProduct->min_quantity;
        $this->category = $this->editingProduct->category;
        $this->supplier = $this->editingProduct->supplier;
        $this->location = $this->editingProduct->location;
        
        $this->showForm = true;
    }
    
    public function save()
    {
        if ($this->editingProduct) {
            $this->rules['sku'] = 'required|unique:products,sku,' . $this->editingProduct->id;
        }
        
        $this->validate();
        
        $data = [
            'sku' => $this->sku,
            'name' => $this->name,
            'slug' => \Str::slug($this->name),
            'description' => $this->description,
            'price' => $this->price,
            'cost' => $this->cost,
            'quantity' => $this->quantity,
            'min_quantity' => $this->min_quantity,
            'category' => $this->category,
            'supplier' => $this->supplier,
            'location' => $this->location,
        ];
        
        if ($this->editingProduct) {
            $this->editingProduct->update($data);
            session()->flash('message', 'Produit mis à jour avec succès.');
        } else {
            Product::create($data);
            session()->flash('message', 'Produit créé avec succès.');
        }
        
        $this->showForm = false;
        $this->reset(['sku', 'name', 'description', 'price', 'cost', 'quantity', 'min_quantity', 'category', 'supplier', 'location']);
    }
    
    public function adjustStock($productId, $adjustment)
    {
        $product = Product::find($productId);
        $product->adjustStock(abs($adjustment), $adjustment > 0 ? 'in' : 'out', 'Ajustement manuel');
        
        session()->flash('message', 'Stock ajusté avec succès.');
    }
    
    public function getLowStockAlertsProperty()
    {
        return Product::lowStock()->take(5)->get();
    }
}