<?php

namespace App\Http\Middleware;

use App\Models\Volunteer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Usage: ->middleware('role:1')        // admin only
     *        ->middleware('role:1,3')      // admin or organisationnel
     *
     * Role IDs:
     *   1 = Administrateur
     *   2 = Intendance
     *   3 = Opérationnel
     *   4 = Réparateur
     *
     * @param string[] $roles  One or more role IDs passed as middleware parameters
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        /** @var Volunteer|null $volunteer */
        $volunteer = $request->user();

        if (!$volunteer instanceof Volunteer) {
            return response()->json(
                ["message" => "Unauthenticated."],
                Response::HTTP_UNAUTHORIZED,
            );
        }

        $allowedIds = array_map("intval", $roles);

        $volunteer->loadMissing("roles");

        $hasRole = $volunteer->roles->contains(
            fn($role) => in_array($role->id, $allowedIds, true),
        );

        if (!$hasRole) {
            return response()->json(
                ["message" => "Forbidden. Insufficient role."],
                Response::HTTP_FORBIDDEN,
            );
        }

        return $next($request);
    }
}
