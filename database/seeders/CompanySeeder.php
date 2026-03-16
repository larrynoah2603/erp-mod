<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Core\Models\Company;
use App\Modules\Core\Models\User;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    public function run()
    {
        // Créer une entreprise par défaut
        $company = Company::create([
            'name' => 'Entreprise Demo',
            'legal_name' => 'Demo SARL',
            'email' => 'contact@demo.com',
            'phone' => '01 23 45 67 89',
            'address' => '123 Rue de la Paix',
            'city' => 'Paris',
            'postal_code' => '75001',
            'country' => 'France',
            'siret' => '12345678901234',
            'vat_number' => 'FR12345678901',
            'settings' => [
                'currency' => 'EUR',
                'timezone' => 'Europe/Paris',
                'date_format' => 'd/m/Y',
            ],
            'working_hours' => [
                'monday' => ['start' => '09:00', 'end' => '18:00'],
                'tuesday' => ['start' => '09:00', 'end' => '18:00'],
                'wednesday' => ['start' => '09:00', 'end' => '18:00'],
                'thursday' => ['start' => '09:00', 'end' => '18:00'],
                'friday' => ['start' => '09:00', 'end' => '17:00'],
                'saturday' => ['start' => null, 'end' => null],
                'sunday' => ['start' => null, 'end' => null],
            ],
            'is_active' => true,
        ]);

        // Créer un utilisateur admin
        $admin = User::create([
            'company_id' => $company->id,
            'first_name' => 'Admin',
            'last_name' => 'System',
            'email' => 'admin@demo.com',
            'password' => Hash::make('password'),
            'phone' => '06 12 34 56 78',
            'hire_date' => now(),
            'job_title' => 'Administrateur',
            'department' => 'Direction',
            'employee_id' => 'EMP001',
            'is_active' => true,
        ]);
        
        
        // Créer un manager
        $manager = User::create([
            'company_id' => $company->id,
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'manager@demo.com',
            'password' => Hash::make('password'),
            'phone' => '06 23 45 67 89',
            'hire_date' => now(),
            'job_title' => 'Chef de département',
            'department' => 'Commercial',
            'employee_id' => 'EMP002',
            'is_active' => true,
        ]);
        
        
        // Créer un employé
        $employee = User::create([
            'company_id' => $company->id,
            'first_name' => 'Marie',
            'last_name' => 'Martin',
            'email' => 'employee@demo.com',
            'password' => Hash::make('password'),
            'phone' => '06 34 56 78 90',
            'hire_date' => now(),
            'job_title' => 'Commercial',
            'department' => 'Commercial',
            'employee_id' => 'EMP003',
            'is_active' => true,
        ]);
        
    }
}