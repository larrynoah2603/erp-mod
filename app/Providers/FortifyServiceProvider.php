<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Modules\Core\Models\Company;
use App\Modules\Core\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        Fortify::loginView(function () {
            return view('auth.login');
        });


        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::authenticateUsing(function (Request $request) {
            $email = Str::lower(trim($request->string('email')->toString()));
            $password = $request->string('password')->toString();

            $user = User::query()
                ->whereRaw('LOWER(email) = ?', [$email])
                ->where('is_active', true)
                ->first();

            if (! $user && app()->isLocal()) {
                $this->ensureLocalDemoUserExists($email);

                $user = User::query()
                    ->whereRaw('LOWER(email) = ?', [$email])
                    ->where('is_active', true)
                    ->first();
            }

            if (! $user || ! Hash::check($password, $user->password)) {
                return null;
            }

            return $user;
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }

    private function ensureLocalDemoUserExists(string $email): void
    {
        $demoUsers = [
            'admin@demo.com' => ['first_name' => 'Admin', 'last_name' => 'System', 'employee_id' => 'EMP001'],
            'manager@demo.com' => ['first_name' => 'Jean', 'last_name' => 'Dupont', 'employee_id' => 'EMP002'],
            'employee@demo.com' => ['first_name' => 'Marie', 'last_name' => 'Martin', 'employee_id' => 'EMP003'],
        ];

        if (! array_key_exists($email, $demoUsers)) {
            return;
        }

        $company = Company::withTrashed()->firstOrCreate(
            ['email' => 'contact@demo.com'],
            ['name' => 'Entreprise Demo', 'legal_name' => 'Demo SARL', 'is_active' => true]
        );

        if ($company->trashed()) {
            $company->restore();
        }

        $meta = $demoUsers[$email];

        $user = User::withTrashed()->firstOrNew(['email' => $email]);
        $user->fill([
            'company_id' => $company->id,
            'first_name' => $meta['first_name'],
            'last_name' => $meta['last_name'],
            'password' => Hash::make('password'),
            'employee_id' => $meta['employee_id'],
            'is_active' => true,
        ]);

        $user->save();

        if ($user->trashed()) {
            $user->restore();
        }
    }
}
