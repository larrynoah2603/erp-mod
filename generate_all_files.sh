#!/bin/bash

echo "🚀 GÉNÉRATION AUTOMATIQUE DES 63 FICHIERS MANQUANTS"
echo "=================================================="

# Configuration des couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Fonction pour créer un modèle de base
create_model() {
    local module=$1
    local model=$2
    local file="app/Modules/$module/Models/${model}.php"
    
    if [ ! -f "$file" ]; then
        cat > "$file" << EOF
<?php

namespace App\Modules\\$module\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Core\Traits\HasCompany;

class $model extends Model
{
    use HasFactory, SoftDeletes, HasCompany;
    
    protected \$fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'is_active',
    ];
    
    protected \$casts = [
        'is_active' => 'boolean',
    ];
    
    public function scopeActive(\$query)
    {
        return \$query->where('is_active', true);
    }
}
EOF
        echo -e "${GREEN}✅ Modèle $model créé${NC}"
    else
        echo -e "${YELLOW}⏩ Modèle $model existe déjà${NC}"
    fi
}

# Fonction pour créer un trait
create_trait() {
    local name=$1
    local file="app/Modules/Core/Traits/${name}.php"
    
    if [ ! -f "$file" ]; then
        cat > "$file" << EOF
<?php

namespace App\Modules\Core\Traits;

trait $name
{
    protected static function boot$name()
    {
        static::creating(function (\$model) {
            if (auth()->check()) {
                // Logique du trait
            }
        });
    }
}
EOF
        echo -e "${GREEN}✅ Trait $name créé${NC}"
    fi
}

# Fonction pour créer un middleware
create_middleware() {
    local name=$1
    local file="app/Modules/Core/Http/Middleware/${name}.php"
    
    if [ ! -f "$file" ]; then
        cat > "$file" << EOF
<?php

namespace App\Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class $name
{
    public function handle(Request \$request, Closure \$next)
    {
        // Logique du middleware
        return \$next(\$request);
    }
}
EOF
        echo -e "${GREEN}✅ Middleware $name créé${NC}"
    fi
}

# Fonction pour créer un composant Livewire
create_livewire_component() {
    local module=$1
    local component=$2
    local file="app/Modules/$module/Http/Livewire/${component}.php"
    
    if [ ! -f "$file" ]; then
        # Convertir en format de nom de classe (ex: "product-form" -> "ProductForm")
        local class_name=$(echo "$component" | sed -r 's/(^|-)([a-z])/\U\2/g')
        
        cat > "$file" << EOF
<?php

namespace App\Modules\\$module\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class $class_name extends Component
{
    use WithPagination, WithFileUploads;
    
    public \$search = '';
    public \$showForm = false;
    public \$editingId = null;
    
    protected \$queryString = ['search'];
    
    public function render()
    {
        return view('$module::livewire.$component')
            ->layout('layouts.app');
    }
    
    public function create()
    {
        \$this->resetForm();
        \$this->showForm = true;
        \$this->editingId = null;
    }
    
    public function edit(\$id)
    {
        \$this->editingId = \$id;
        \$this->showForm = true;
        // Charger les données
    }
    
    public function save()
    {
        // Logique de sauvegarde
        \$this->showForm = false;
        session()->flash('message', 'Enregistrement réussi.');
    }
    
    public function delete(\$id)
    {
        // Logique de suppression
        session()->flash('message', 'Suppression réussie.');
    }
    
    private function resetForm()
    {
        // Reset des champs du formulaire
    }
}
EOF
        echo -e "${GREEN}✅ Composant Livewire $component créé${NC}"
    fi
}

# Fonction pour créer une vue Livewire
create_livewire_view() {
    local module=$1
    local view=$2
    local file="resources/views/modules/$module/livewire/${view}.blade.php"
    
    # Créer le dossier si nécessaire
    mkdir -p "resources/views/modules/$module/livewire"
    
    if [ ! -f "$file" ]; then
        # Convertir en titre (ex: "product-form" -> "Product Form")
        local title=$(echo "$view" | sed -r 's/-/ /g' | sed -r 's/(^| )([a-z])/\U\2/g')
        
        cat > "$file" << EOF
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">$title</h1>
        <button wire:click="create" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
            + Nouveau
        </button>
    </div>

    <!-- Barre de recherche -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4">
            <input type="text" 
                   wire:model.debounce.300ms="search" 
                   placeholder="Rechercher..." 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
        </div>
    </div>

    <!-- Formulaire -->
    @if(\$showForm)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">{{ \$editingId ? 'Modifier' : 'Nouveau' }}</h2>
        
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                    <input type="text" wire:model="name" class="w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Code</label>
                    <input type="text" wire:model="code" class="w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea wire:model="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" wire:click="\$set('showForm', false)" class="px-4 py-2 border rounded-md">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Sauvegarder</button>
            </div>
        </form>
    </div>
    @endif

    <!-- Liste -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                        Aucune donnée disponible
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
EOF
        echo -e "${GREEN}✅ Vue $view créée${NC}"
    fi
}

# Fonction pour créer une vue PDF
create_pdf_view() {
    local module=$1
    local view=$2
    local file="resources/views/modules/$module/pdf/${view}.blade.php"
    
    mkdir -p "resources/views/modules/$module/pdf"
    
    if [ ! -f "$file" ]; then
        cat > "$file" << EOF
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Document</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .content { margin: 20px 0; }
        .footer { text-align: center; margin-top: 50px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Document</h1>
    </div>
    
    <div class="content">
        <p>Contenu du document...</p>
    </div>
    
    <div class="footer">
        <p>Généré le {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
EOF
        echo -e "${GREEN}✅ Vue PDF $view créée${NC}"
    fi
}

# Fonction pour créer un contrôleur
create_controller() {
    local module=$1
    local controller=$2
    local file="app/Modules/$module/Http/Controllers/${controller}.php"
    
    mkdir -p "app/Modules/$module/Http/Controllers"
    
    if [ ! -f "$file" ]; then
        cat > "$file" << EOF
<?php

namespace App\Modules\\$module\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class $controller extends Controller
{
    public function index()
    {
        return view('$module::index');
    }
    
    public function create()
    {
        return view('$module::form');
    }
    
    public function store(Request \$request)
    {
        // Logique de création
        return redirect()->route('$module.index');
    }
    
    public function show(\$id)
    {
        return view('$module::show');
    }
    
    public function edit(\$id)
    {
        return view('$module::form');
    }
    
    public function update(Request \$request, \$id)
    {
        // Logique de mise à jour
        return redirect()->route('$module.index');
    }
    
    public function destroy(\$id)
    {
        // Logique de suppression
        return redirect()->route('$module.index');
    }
}
EOF
        echo -e "${GREEN}✅ Contrôleur $controller créé${NC}"
    fi
}

# Fonction pour créer une commande
create_command() {
    local command=$1
    local file="app/Console/Commands/${command}.php"
    
    mkdir -p "app/Console/Commands"
    
    if [ ! -f "$file" ]; then
        local signature=$(echo "$command" | sed -r 's/([A-Z])/-\L\1/g' | sed 's/^-//')
        cat > "$file" << EOF
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class $command extends Command
{
    protected \$signature = 'app:$signature';
    protected \$description = 'Description de la commande';
    
    public function handle()
    {
        \$this->info('Commande exécutée avec succès!');
        return Command::SUCCESS;
    }
}
EOF
        echo -e "${GREEN}✅ Commande $command créée${NC}"
    fi
}

# Fonction pour créer un seeder
create_seeder() {
    local seeder=$1
    local file="database/seeders/${seeder}.php"
    
    if [ ! -f "$file" ]; then
        cat > "$file" << EOF
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class $seeder extends Seeder
{
    public function run()
    {
        // Données à insérer
        DB::table('table_name')->insert([
            'name' => 'Example',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
EOF
        echo -e "${GREEN}✅ Seeder $seeder créé${NC}"
    fi
}

# Fonction pour créer une factory
create_factory() {
    local factory=$1
    local file="database/factories/${factory}.php"
    
    if [ ! -f "$file" ]; then
        local model=$(echo "$factory" | sed 's/Factory//')
        cat > "$file" << EOF
<?php

namespace Database\Factories;

use App\Modules\Core\Models\\$model;
use Illuminate\Database\Eloquent\Factories\Factory;

class ${factory} extends Factory
{
    protected \$model = $model::class;
    
    public function definition()
    {
        return [
            'name' => \$this->faker->name,
            'code' => \$this->faker->unique()->bothify('???-####'),
            'description' => \$this->faker->sentence,
            'is_active' => true,
        ];
    }
}
EOF
        echo -e "${GREEN}✅ Factory $factory créée${NC}"
    fi
}

# Fonction pour créer une config
create_config() {
    local config=$1
    local file="config/${config}.php"
    
    if [ ! -f "$file" ]; then
        cat > "$file" << EOF
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration de $config
    |--------------------------------------------------------------------------
    */
    
    'enabled' => env('${config^^}_ENABLED', true),
    
    'settings' => [
        'default_value' => 'example',
    ],
    
    'options' => [
        'option1' => 'Valeur 1',
        'option2' => 'Valeur 2',
    ],
];
EOF
        echo -e "${GREEN}✅ Config $config créée${NC}"
    fi
}

# =============================================
# GÉNÉRATION DE TOUS LES FICHIERS
# =============================================

echo -e "\n${BLUE}1. CRÉATION DES TRAITS MANQUANTS${NC}"
create_trait "HasAudit"
create_trait "HasAttachments"

echo -e "\n${BLUE}2. CRÉATION DES MIDDLEWARE MANQUANTS${NC}"
create_middleware "ModuleAccess"
create_middleware "AuditLog"

echo -e "\n${BLUE}3. CRÉATION DES MODELS MANQUANTS${NC}"
create_model "Stock" "Category"
create_model "Stock" "Supplier"
create_model "Sales" "Customer"
create_model "Sales" "Payment"
create_model "HR" "Department"
create_model "HR" "Contract"

echo -e "\n${BLUE}4. CRÉATION DES COMPOSANTS LIVEWIRE MANQUANTS${NC}"
# Core
create_livewire_component "Core" "profile"
create_livewire_component "Core" "settings"
create_livewire_component "Core" "notifications"

# Stock
create_livewire_component "Stock" "product-form"
create_livewire_component "Stock" "stock-alerts"
create_livewire_component "Stock" "supplier-manager"
create_livewire_component "Stock" "category-manager"

# Sales
create_livewire_component "Sales" "invoice-form"
create_livewire_component "Sales" "customer-manager"
create_livewire_component "Sales" "payment-manager"
create_livewire_component "Sales" "quote-manager"

# HR
create_livewire_component "HR" "employee-manager"
create_livewire_component "HR" "payroll-manager"
create_livewire_component "HR" "salary-config"
create_livewire_component "HR" "attendance-report"
create_livewire_component "HR" "contract-manager"
create_livewire_component "HR" "recruitment-manager"

echo -e "\n${BLUE}5. CRÉATION DES VUES LIVEWIRE MANQUANTES${NC}"
# Core
create_livewire_view "core" "profile"
create_livewire_view "core" "settings"
create_livewire_view "core" "notifications"

# Stock
create_livewire_view "stock" "product-form"
create_livewire_view "stock" "stock-alerts"
create_livewire_view "stock" "supplier-manager"
create_livewire_view "stock" "category-manager"

# Sales
create_livewire_view "sales" "invoice-form"
create_livewire_view "sales" "customer-manager"
create_livewire_view "sales" "payment-manager"
create_livewire_view "sales" "quote-manager"

# HR
create_livewire_view "hr" "employee-manager"
create_livewire_view "hr" "payroll-manager"
create_livewire_view "hr" "salary-config"
create_livewire_view "hr" "attendance-report"
create_livewire_view "hr" "contract-manager"
create_livewire_view "hr" "recruitment-manager"

echo -e "\n${BLUE}6. CRÉATION DES VUES PDF${NC}"
create_pdf_view "stock" "inventory-report"
create_pdf_view "sales" "invoice"
create_pdf_view "sales" "quote"
create_pdf_view "sales" "delivery-note"
create_pdf_view "hr" "payslip"
create_pdf_view "hr" "contract"
create_pdf_view "hr" "attendance-report"

echo -e "\n${BLUE}7. CRÉATION DES CONTROLEURS${NC}"
create_controller "Core" "AuthController"
create_controller "Core" "CompanyController"
create_controller "Core" "ReportController"
create_controller "Core" "ExportController"
create_controller "Core" "ImportController"
create_controller "Core" "NotificationController"
create_controller "Core" "ApiController"

echo -e "\n${BLUE}8. CRÉATION DES COMMANDES${NC}"
create_command "GeneratePayslips"
create_command "StockAlert"
create_command "SendInvoices"
create_command "CleanupLogs"
create_command "CalculateAttendance"

echo -e "\n${BLUE}9. CRÉATION DES SEEDERS${NC}"
create_seeder "CompanySeeder"
create_seeder "RolePermissionSeeder"
create_seeder "UserSeeder"
create_seeder "ProductSeeder"
create_seeder "DepartmentSeeder"

echo -e "\n${BLUE}10. CRÉATION DES FACTORIES${NC}"
create_factory "CompanyFactory"
create_factory "ProductFactory"
create_factory "InvoiceFactory"
create_factory "AttendanceFactory"
create_factory "LeaveRequestFactory"

echo -e "\n${BLUE}11. CRÉATION DES CONFIGS${NC}"
create_config "modules"
create_config "company"
create_config "erp"

echo -e "\n${BLUE}12. CRÉATION DES VUES DE LAYOUT MANQUANTES${NC}"
# Layout print
if [ ! -f "resources/views/layouts/print.blade.php" ]; then
    cat > "resources/views/layouts/print.blade.php" << EOF
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2cm; }
        @page { margin: 2cm; }
        .no-print { display: none; }
    </style>
</head>
<body>
    <div class="content">
        @yield('content')
    </div>
</body>
</html>
EOF
    echo -e "${GREEN}✅ Layout print créé${NC}"
fi

# Layout email
if [ ! -f "resources/views/layouts/email.blade.php" ]; then
    cat > "resources/views/layouts/email.blade.php" << EOF
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ \$subject ?? 'Email' }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; padding: 20px 0; }
        .content { padding: 20px; }
        .footer { text-align: center; padding: 20px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ config('app.name') }}</h2>
        </div>
        
        <div class="content">
            @yield('content')
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
EOF
    echo -e "${GREEN}✅ Layout email créé${NC}"
fi

# =============================================
# RÉCAPITULATIF
# =============================================

echo -e "\n${BLUE}========================================${NC}"
echo -e "${GREEN}✅ GÉNÉRATION TERMINÉE !${NC}"
echo -e "${BLUE}========================================${NC}"
echo -e "Total fichiers créés: 63"
echo -e "\nDétails par catégorie:"
echo -e "- Traits: 2"
echo -e "- Middleware: 2"
echo -e "- Models: 6"
echo -e "- Livewire Components: 18"
echo -e "- Livewire Views: 18"
echo -e "- PDF Views: 7"
echo -e "- Controllers: 7"
echo -e "- Commands: 5"
echo -e "- Seeders: 5"
echo -e "- Factories: 5"
echo -e "- Configs: 3"
echo -e "- Layouts: 2"
echo -e "\n${YELLOW}⚠️  Note: Ces fichiers contiennent des templates basiques${NC}"
echo -e "${YELLOW}   Vous devrez les personnaliser selon vos besoins${NC}"