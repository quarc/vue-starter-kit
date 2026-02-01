<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Notifications\ContactInformationUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class AvatarController extends Controller
{
    /**
     * Upload and update the user's avatar.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', File::image()->max(2 * 1024)], // Max 2MB
        ]);

        $user = $request->user();

        // Delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->getRawOriginal('avatar'));
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');

        // Update user avatar path
        $user->update([
            'avatar' => $path,
        ]);

        $user->notify(new ContactInformationUpdated(['Avatar']));

        return back()->with('status', 'avatar-updated');
    }

    /**
     * Delete the user's avatar.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar) {
            // Delete avatar file from storage
            Storage::disk('public')->delete($user->getRawOriginal('avatar'));

            // Clear avatar path from database
            $user->update([
                'avatar' => null,
            ]);

            $user->notify(new ContactInformationUpdated(['Avatar']));
        }

        return back()->with('status', 'avatar-deleted');
    }
}
