<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Notifications\SessionsTerminated;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class SessionsController extends Controller
{
    /**
     * Get all user sessions.
     */
    public function index(Request $request): array
    {
        $sessions = $this->getSessions($request);

        return [
            'sessions' => $sessions,
        ];
    }

    /**
     * Delete a specific session.
     */
    public function destroy(Request $request, string $sessionId)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        // Don't allow deleting current session
        if ($sessionId === $request->session()->getId()) {
            return back()->withErrors([
                'session' => __('You cannot delete your current session.'),
            ]);
        }

        $deleted = DB::connection(config('session.connection'))
            ->table(config('session.table', 'sessions'))
            ->where('id', $sessionId)
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->delete();

        if ($deleted > 0) {
            $request->user()->notify(new SessionsTerminated($deleted));
        }

        return back();
    }

    /**
     * Delete all other sessions except current.
     */
    public function destroyOthers(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $deleted = DB::connection(config('session.connection'))
            ->table(config('session.table', 'sessions'))
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        if ($deleted > 0) {
            $request->user()->notify(new SessionsTerminated($deleted));
        }

        return back();
    }

    /**
     * Get all sessions for the current user.
     */
    protected function getSessions(Request $request): array
    {
        if (config('session.driver') !== 'database') {
            return [];
        }

        $sessions = DB::connection(config('session.connection'))
            ->table(config('session.table', 'sessions'))
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->orderBy('last_activity', 'desc')
            ->get();

        return $sessions->map(function ($session) use ($request) {
            $agent = $this->createAgent($session);

            return [
                'id' => $session->id,
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === $request->session()->getId(),
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                'last_active_at' => Carbon::createFromTimestamp($session->last_activity)->toIso8601String(),
                'agent' => [
                    'is_desktop' => $agent->isDesktop(),
                    'is_mobile' => $agent->isMobile(),
                    'is_tablet' => $agent->isTablet(),
                    'platform' => $agent->platform(),
                    'browser' => $agent->browser(),
                    'browser_version' => $agent->version($agent->browser()),
                ],
            ];
        })->all();
    }

    /**
     * Create a new agent instance from session data.
     */
    protected function createAgent(object $session): Agent
    {
        $agent = new Agent;
        $agent->setUserAgent($session->user_agent);

        return $agent;
    }
}
