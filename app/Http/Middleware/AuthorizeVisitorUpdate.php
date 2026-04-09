<?php

namespace App\Http\Middleware;

use App\Http\Services\VisitorTokenService;
use App\Models\Visitor;
use App\Models\Volunteer;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeVisitorUpdate
{
    public function __construct(private VisitorTokenService $tokenService) {}

    /**
     * Allow the request through if EITHER:
     *   1. The request carries a valid visitor edit token for this visitor, OR
     *   2. The request is authenticated via Sanctum with role Administrateur (1)
     *      or Opérationnel (3).
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ------------------------------------------------------------------
        // Path 1 – visitor self-edit token
        // ------------------------------------------------------------------
        $plainToken =
            $request->input("visitor_token") ??
            $request->header("X-Visitor-Token");

        if ($plainToken !== null) {
            /** @var Visitor|null $visitor */
            $visitor = $request->route("visitor");

            if (!$visitor instanceof Visitor) {
                return response()->json(
                    ["message" => "Visiteur introuvable."],
                    Response::HTTP_NOT_FOUND,
                );
            }

            if (!$this->tokenService->isValid($visitor, $plainToken)) {
                return response()->json(
                    ["message" => "Token invalide ou expiré."],
                    Response::HTTP_UNAUTHORIZED,
                );
            }

            // Valid token – proceed without a Sanctum user (actor will be null)
            return $next($request);
        }

        // ------------------------------------------------------------------
        // Path 2 – Sanctum authentication + role check
        // ------------------------------------------------------------------
        /** @var Volunteer|null $volunteer */
        $volunteer = Auth::guard("sanctum")->user();

        if (!$volunteer instanceof Volunteer) {
            return response()->json(
                ["message" => "Unauthenticated."],
                Response::HTTP_UNAUTHORIZED,
            );
        }

        $volunteer->loadMissing("roles");

        $allowed = [1, 3]; // Administrateur, Opérationnel
        $hasRole = $volunteer->roles->contains(
            fn($role) => in_array($role->id, $allowed, true),
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
