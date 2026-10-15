<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Allow a signed-in administrator, or the person who just passed
 * biometric verification, to view that person's record.
 *
 * Face and voice recognition store `verified_person_id` and
 * `verified_at` in the session on a successful match.
 */
class EnsureVerifiedPerson
{
    public const SESSION_PERSON = 'verified_person_id';

    public const SESSION_TIME = 'verified_at';

    public const VALID_FOR_MINUTES = 15;

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            return $next($request);
        }

        $person = $request->route('person');
        $personId = is_object($person) ? $person->getKey() : $person;

        if (self::isVerified($request, (int) $personId)) {
            return $next($request);
        }

        return redirect()
            ->route('biometric.login')
            ->with('error', 'Please verify your identity to view this record.');
    }

    public static function isVerified(Request $request, int $personId): bool
    {
        $session = $request->session();

        return (int) $session->get(self::SESSION_PERSON) === $personId
            && (int) $session->get(self::SESSION_TIME, 0)
                >= now()->subMinutes(self::VALID_FOR_MINUTES)->getTimestamp();
    }

    public static function markVerified(Request $request, int $personId): void
    {
        // Administrators are already authenticated.
        if ($request->user()) {
            return;
        }

        // New session ID against fixation. migrate() keeps the CSRF
        // token, unlike regenerate(), so the page's next request
        // does not fail with 419.
        $request->session()->migrate(true);

        $request->session()->put([
            self::SESSION_PERSON => $personId,
            self::SESSION_TIME => now()->getTimestamp(),
        ]);
    }
}
