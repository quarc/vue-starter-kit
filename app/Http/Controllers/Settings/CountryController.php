<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Notifications\ContactInformationUpdated;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Update the user's country.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'country' => ['nullable', 'string', 'size:2'],
            'currency' => ['nullable', 'string', 'size:3'],
        ]);

        $data = [];

        if (array_key_exists('country', $validated)) {
            $data['country'] = $validated['country'] ? strtoupper($validated['country']) : null;
        }

        if (array_key_exists('currency', $validated)) {
            $data['currency'] = $validated['currency'] ? strtoupper($validated['currency']) : null;
        }

        if (empty($data)) {
            return back()->with('success', __('Region settings updated successfully.'));
        }

        $user = $request->user();
        $user->fill($data);

        $changedFields = [];

        if ($user->isDirty('country')) {
            $changedFields[] = 'Country';
        }

        if ($user->isDirty('currency')) {
            $changedFields[] = 'Currency';
        }

        if ($user->isDirty()) {
            $user->save();

            if (! empty($changedFields)) {
                $user->notify(new ContactInformationUpdated($changedFields));
            }
        }

        return back()->with('success', __('Region settings updated successfully.'));
    }
}
