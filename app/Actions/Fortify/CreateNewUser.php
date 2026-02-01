<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Symfony\Component\Intl\Currencies;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        $currency = null;

        if (! empty($input['country'])) {
            try {
                $currency = Currencies::forCountry($input['country'])[0];
            } catch (\Exception $e) {
                // Fail silently if country or currency data isn't found
            }
        }

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'timezone' => $input['timezone'] ?? null,
            'locale' => $input['locale'] ?? null,
            'country' => $input['country'] ?? null,
            'currency' => $currency,
        ]);
    }
}
