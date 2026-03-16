<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Company;
use App\Modules\Core\Models\Permission;
use App\Modules\Core\Models\Role;
use App\Modules\Core\Models\User;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view_dashboard',
            'manage_company',
            'view_reports',
            'view_stock',
            'create_product',
            'edit_product',
            'delete_product',
            'manage_inventory',
            'view_alerts',
            'view_sales',
            'create_invoice',
            'edit_invoice',
            'delete_invoice',
            'manage_customers',
            'process_payments',
            'view_hr',
            'manage_attendance',
            'manage_leaves',
            'manage_payroll',
            'manage_employees',
            'view_hr_dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        foreach (Company::query()->get() as $company) {
            $adminRole = Role::firstOrCreate(
                ['name' => 'admin', 'guard_name' => 'web', 'company_id' => $company->id],
                ['display_name' => 'Administrateur', 'description' => 'Accès complet à tous les modules']
            );
            $adminRole->syncPermissions(Permission::all());

            $managerRole = Role::firstOrCreate(
                ['name' => 'manager', 'guard_name' => 'web', 'company_id' => $company->id],
                ['display_name' => 'Manager', 'description' => 'Gestion opérationnelle']
            );
            $managerRole->syncPermissions([
                'view_dashboard',
                'view_stock', 'create_product', 'edit_product', 'manage_inventory', 'view_alerts',
                'view_sales', 'create_invoice', 'edit_invoice', 'manage_customers',
                'view_hr', 'manage_attendance', 'manage_leaves', 'view_hr_dashboard',
            ]);

            $employeeRole = Role::firstOrCreate(
                ['name' => 'employee', 'guard_name' => 'web', 'company_id' => $company->id],
                ['display_name' => 'Employé', 'description' => 'Accès de base']
            );
            $employeeRole->syncPermissions(['view_dashboard', 'view_stock', 'view_hr']);

            User::query()->where('company_id', $company->id)->where('email', 'admin@demo.com')->first()?->syncRoles([$adminRole]);
            User::query()->where('company_id', $company->id)->where('email', 'manager@demo.com')->first()?->syncRoles([$managerRole]);
            User::query()->where('company_id', $company->id)->where('email', 'employee@demo.com')->first()?->syncRoles([$employeeRole]);
        }
    }
}
