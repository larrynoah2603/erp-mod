<?php

namespace App\Modules\Sales\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Modules\Sales\Models\Invoice;
use App\Modules\Sales\Models\InvoiceItem;
use App\Modules\Stock\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceManager extends Component
{
    use WithPagination;
    
    public $search = '';
    public $status = '';
    public $sortField = 'issue_date';
    public $sortDirection = 'desc';
    
    public $showForm = false;
    public $editingInvoice = null;
    
    // Form fields
    public $client_name;
    public $client_email;
    public $client_phone;
    public $client_address;
    public $client_vat;
    public $issue_date;
    public $due_date;
    public $notes;
    public $tax_rate = 20;
    
    public $items = [];
    
    protected $rules = [
        'client_name' => 'required|min:3',
        'client_email' => 'nullable|email',
        'issue_date' => 'required|date',
        'due_date' => 'required|date|after:issue_date',
        'items' => 'required|array|min:1',
        'items.*.description' => 'required',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.unit_price' => 'required|numeric|min:0',
    ];
    
    public function mount()
    {
        $this->issue_date = now()->format('Y-m-d');
        $this->due_date = now()->addDays(30)->format('Y-m-d');
        $this->addItem();
    }
    
    public function render()
    {
        $invoices = Invoice::with('user', 'items')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('invoice_number', 'like', '%' . $this->search . '%')
                      ->orWhere('client_name', 'like', '%' . $this->search . '%')
                      ->orWhere('client_email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);
            
        $products = Product::where('is_active', true)->get();
        
        $stats = [
            'total_draft' => Invoice::where('status', 'draft')->count(),
            'total_sent' => Invoice::where('status', 'sent')->count(),
            'total_paid' => Invoice::where('status', 'paid')->sum('total'),
            'total_overdue' => Invoice::overdue()->count(),
        ];
        
        return view('sales::livewire.invoice-manager', compact('invoices', 'products', 'stats'));
    }
    
    public function addItem()
    {
        $this->items[] = [
            'product_id' => null,
            'description' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'discount' => 0,
            'tax_rate' => $this->tax_rate,
        ];
    }
    
    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }
    
    public function selectProduct($index, $productId)
    {
        $product = Product::find($productId);
        if ($product) {
            $this->items[$index]['description'] = $product->name;
            $this->items[$index]['unit_price'] = $product->price;
        }
    }
    
    public function calculateSubtotal()
    {
        $subtotal = 0;
        foreach ($this->items as $item) {
            $subtotal += ($item['unit_price'] * $item['quantity']) - ($item['discount'] ?? 0);
        }
        return $subtotal;
    }
    
    public function calculateTax()
    {
        $tax = 0;
        foreach ($this->items as $item) {
            $subtotal = ($item['unit_price'] * $item['quantity']) - ($item['discount'] ?? 0);
            $tax += $subtotal * ($item['tax_rate'] / 100);
        }
        return $tax;
    }
    
    public function calculateTotal()
    {
        return $this->calculateSubtotal() + $this->calculateTax();
    }
    
    public function save()
    {
        $this->validate();
        
        $invoice = Invoice::create([
            'user_id' => auth()->id(),
            'client_name' => $this->client_name,
            'client_email' => $this->client_email,
            'client_phone' => $this->client_phone,
            'client_address' => $this->client_address,
            'client_vat' => $this->client_vat,
            'issue_date' => $this->issue_date,
            'due_date' => $this->due_date,
            'subtotal' => $this->calculateSubtotal(),
            'tax_rate' => $this->tax_rate,
            'tax_amount' => $this->calculateTax(),
            'total' => $this->calculateTotal(),
            'status' => 'draft',
            'notes' => $this->notes,
        ]);
        
        foreach ($this->items as $item) {
            $invoice->items()->create([
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount' => $item['discount'] ?? 0,
                'tax_rate' => $item['tax_rate'],
            ]);
        }
        
        session()->flash('message', 'Facture créée avec succès.');
        $this->reset();
        $this->showForm = false;
    }
    
    public function markAsSent($id)
    {
        $invoice = Invoice::find($id);
        $invoice->update(['status' => 'sent']);
        session()->flash('message', 'Facture marquée comme envoyée.');
    }
    
    public function markAsPaid($id)
    {
        $invoice = Invoice::find($id);
        $invoice->markAsPaid();
        session()->flash('message', 'Facture marquée comme payée.');
    }
    
    public function downloadPdf($id)
    {
        $invoice = Invoice::with('items', 'company', 'user')->find($id);
        
        $pdf = Pdf::loadView('sales::pdf.invoice', ['invoice' => $invoice]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "facture-{$invoice->invoice_number}.pdf");
    }
}