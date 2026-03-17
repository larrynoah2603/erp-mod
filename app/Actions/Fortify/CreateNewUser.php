<?php

namespace App\Actions\Fortify;

use App\Modules\Core\Models\Company;
use App\Modules\Core\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['nullable', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->after(function ($validator) use ($input) {
            if (empty($input['name']) && empty($input['first_name']) && empty($input['last_name'])) {
                $validator->errors()->add('name', __('Le nom est requis.'));
            }
        })->validate();

        [$firstName, $lastName] = $this->resolveNames($input);

        $company = Company::query()->firstOrCreate(
            ['email' => 'contact@demo.com'],
            ['name' => 'Entreprise Demo', 'legal_name' => 'Demo SARL', 'is_active' => true]
        );

        return User::create([
            'company_id' => $company->id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'is_active' => true,
        ]);
    }

    /**
     * @param  array<string, string>  $input
     * @return array{0:string,1:string}
     */
    private function resolveNames(array $input): array
    {
        if (! empty($input['first_name']) || ! empty($input['last_name'])) {
            return [trim((string) ($input['first_name'] ?? 'Utilisateur')), trim((string) ($input['last_name'] ?? 'ERP'))];
        }

        $name = trim((string) ($input['name'] ?? 'Utilisateur ERP'));
        $parts = preg_split('/\s+/', $name) ?: [];
        $first = array_shift($parts) ?: 'Utilisateur';
        $last = implode(' ', $parts) ?: 'ERP';

        return [$first, $last];
    }
}
