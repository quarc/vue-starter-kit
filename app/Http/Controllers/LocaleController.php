<?php

namespace App\Http\Controllers;

use App\Notifications\ContactInformationUpdated;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Switch the application locale.
     */
    public function __invoke(Request $request)
    {
        $locale = $request->input('locale');
        $supportedLocales = config('app.supported_locales', ['en']);

        // Validate locale
        if (! in_array($locale, $supportedLocales)) {
            return back()->withErrors([
                'locale' => 'Unsupported locale.',
            ]);
        }

        // If user is authenticated, update their locale in DB
        if ($request->user() && $request->user()->locale !== $locale) {
            $request->user()->forceFill([
                'locale' => $locale,
            ])->save();

            $request->user()->notify(new ContactInformationUpdated(['Language']));
        }

        // Always update session for both guests and authenticated users
        session(['locale' => $locale]);

        // Redirect back to the previous page
        return back();
    }
}
