<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Notifications\ContactInformationUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DateTimeController extends Controller
{
    /**
     * Show the user's date and time settings page.
     */
    public function edit(): Response
    {
        return Inertia::render('settings/DateTime', [
            'currentTimezone' => auth()->user()->timezone,
            'currentDateFormat' => auth()->user()->date_format,
            'currentTimeFormat' => auth()->user()->time_format,
        ]);
    }

    /**
     * Update the user's date and time settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'timezone' => ['nullable', 'string', 'timezone'],
            'date_format' => ['nullable', 'string', 'max:50'],
            'time_format' => ['nullable', 'string', 'max:50'],
        ]);

        $user = $request->user();
        $user->fill($validated);

        $changedFields = [];

        if ($user->isDirty('timezone')) {
            $changedFields[] = 'Timezone';
        }

        if ($user->isDirty('date_format')) {
            $changedFields[] = 'Date format';
        }

        if ($user->isDirty('time_format')) {
            $changedFields[] = 'Time format';
        }

        if ($user->isDirty()) {
            $user->save();

            if (! empty($changedFields)) {
                $user->notify(new ContactInformationUpdated($changedFields));
            }
        }

        return back();
    }
}
