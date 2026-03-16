<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Core\Models\Role;
use App\Modules\Core\Models\Permission;
use App\Modules\Core\Models\Company;
use App\Modules\Core\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les permissions par module
        $permissions = [
            // Core
            'view_dashboard',
            'manage_company',
            'view_reports',
            
            // Stock
            'view_stock',
            'create_product',
            'edit_product',
            'delete_product',
            'manage_inventory',
            'view_alerts',
            
            // Sales
            'view_sales',
            'create_invoice',
            'edit_invoice',
            'delete_invoice',
            'manage_customers',
            'process_payments',
            
            // HR
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

        // Créer les rôles pour chaque entreprise
        $companies = Company::all();
        
        foreach ($companies as $company) {
            // Admin role
            $adminRole = Role::create([
                'name' => 'admin',
                'guard_name' => 'web',
                'company_id' => $company->id,
                'display_name' => 'Administrateur',
                'description' => 'Accès complet à tous les modules'
            ]);
            $adminRole->givePermissionTo(Permission::all());

            // Manager role
            $managerRole = Role::create([
                'name' => 'manager',
                'guard_name' => 'web',
                'company_id' => $company->id,
                'display_name' => 'Manager',
                'description' => 'Gestion opérationnelle'
            ]);
            $managerRole->givePermissionTo([
                'view_dashboard',
                'view_stock', 'create_product', 'edit_product', 'manage_inventory', 'view_alerts',
                'view_sales', 'create_invoice', 'edit_invoice', 'manage_customers',
                'view_hr', 'manage_attendance', 'manage_leaves', 'view_hr_dashboard'
            ]);

            // Employee role
            $employeeRole = Role::create([
                'name' => 'employee',
                'guard_name' => 'web',
                'company_id' => $company->id,
                'display_name' => 'Employé',
                'description' => 'Accès de base'
            ]);
            $employeeRole->givePermissionTo([
                'view_dashboard',
                'view_stock',
                'view_hr',
            ]);
            // Assigner les rôles aux utilisateurs de démonstration de l'entreprise
            User::where('company_id', $company->id)->where('email', 'admin@demo.com')->first()?->syncRoles([$adminRole]);
            User::where('company_id', $company->id)->where('email', 'manager@demo.com')->first()?->syncRoles([$managerRole]);
            User::where('company_id', $company->id)->where('email', 'employee@demo.com')->first()?->syncRoles([$employeeRole]);
        }
    }
}