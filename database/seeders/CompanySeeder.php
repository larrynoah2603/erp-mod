<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Company;
use App\Modules\Core\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::withTrashed()->firstOrNew(['email' => 'contact@demo.com']);
        $company->fill([
            'name' => 'Entreprise Demo',
            'legal_name' => 'Demo SARL',
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
        $company->save();

        if ($company->trashed()) {
            $company->restore();
        }

        $this->upsertDemoUser($company->id, 'admin@demo.com', 'Admin', 'System', 'Administrateur', 'Direction', 'EMP001', '06 12 34 56 78');
        $this->upsertDemoUser($company->id, 'manager@demo.com', 'Jean', 'Dupont', 'Chef de département', 'Commercial', 'EMP002', '06 23 45 67 89');
        $this->upsertDemoUser($company->id, 'employee@demo.com', 'Marie', 'Martin', 'Commercial', 'Commercial', 'EMP003', '06 34 56 78 90');
    }

    private function upsertDemoUser(
        int $companyId,
        string $email,
        string $firstName,
        string $lastName,
        string $jobTitle,
        string $department,
        string $employeeId,
        string $phone,
    ): void {
        $user = User::withTrashed()->firstOrNew(['email' => $email]);
        $user->fill([
            'company_id' => $companyId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'password' => Hash::make('password'),
            'phone' => $phone,
            'hire_date' => now(),
            'job_title' => $jobTitle,
            'department' => $department,
            'employee_id' => $employeeId,
            'is_active' => true,
        ]);
        $user->save();

        if ($user->trashed()) {
            $user->restore();
        }
    }
}
