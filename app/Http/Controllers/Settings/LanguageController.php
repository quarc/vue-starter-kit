<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Intl\Languages;

class LanguageController extends Controller
{
    /**
     * Show the user's language settings page.
     */
    public function edit(): Response
    {
        $supportedLocales = config('app.supported_locales', ['en']);

        // Map locale codes to language names using PHP's Locale class
        $languages = collect($supportedLocales)->map(function ($locale) {
            return [
                'value' => $locale,
                'label' => mb_ucfirst(Languages::getName($locale, app()->getLocale())),
            ];
        })->toArray();

        $countries = collect(Countries::getNames(app()->getLocale()))
            ->map(function ($label, $code) {
                return [
                    'value' => $code,
                    'label' => $label,
                ];
            })
            ->sortBy('label')
            ->values()
            ->toArray();

        // Get all available currencies
        $currencies = collect(Currencies::getNames(app()->getLocale()))
            ->map(function ($name, $code) {
                try {
                    return [
                        'value' => $code,
                        'name' => mb_ucfirst($name),
                    ];
                } catch (\Throwable $e) {
                    return null;
                }
            })
            ->filter()
            ->sortBy('name')
            ->values()
            ->toArray();

        // Generate country to currency map
        $countryToCurrency = collect($countries)
            ->mapWithKeys(function ($country) {
                try {
                    $currencyCodes = Currencies::forCountry($country['value']);

                    return [$country['value'] => $currencyCodes];
                } catch (\Throwable $e) {
                    return [$country['value'] => []];
                }
            })
            ->filter()
            ->toArray();

        return Inertia::render('settings/LanguageAndRegion', [
            'languages' => $languages,
            'countries' => $countries,
            'currencies' => $currencies,
            'countryToCurrency' => $countryToCurrency,
            'currentLocale' => auth()->user()->locale ?? app()->getLocale(),
            'currentCountry' => auth()->user()->country ?? null,
            'currentCurrency' => auth()->user()->currency ?? null,
        ]);
    }
}
