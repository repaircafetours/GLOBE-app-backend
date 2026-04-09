<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginVolunteerRequest;
use App\Models\Volunteer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Authenticate a volunteer and return a Sanctum API token.
     *
     * Accepts either:
     *   { "login": "admin", "password": "secret" }
     *   { "idHumHub": 42,   "password": "secret" }
     *
     * The "login" field is checked first; "idHumHub" is used as fallback.
     */
    public function login(LoginVolunteerRequest $request): JsonResponse
    {
        $volunteer = null;

        if ($request->filled("login")) {
            $volunteer = Volunteer::where(
                "login",
                $request->input("login"),
            )->first();
        } elseif ($request->filled("idHumHub")) {
            $volunteer = Volunteer::where(
                "idHumHub",
                $request->integer("idHumHub"),
            )->first();
        }

        if (
            !$volunteer ||
            !$volunteer->password ||
            !Hash::check($request->string("password"), $volunteer->password)
        ) {
            return response()->json(["message" => "Invalide identifiant"], 401);
        }

        // Revoke all previous tokens for this volunteer (single-session policy)
        $volunteer->tokens()->delete();

        $token = $volunteer->createToken("api_token")->plainTextToken;

        return response()->json([
            "token" => $token,
            "token_type" => "Bearer",
            "volunteer" => [
                "id" => $volunteer->id,
                "idHumHub" => $volunteer->idHumHub,
                "login" => $volunteer->login,
                "roles" => $volunteer->roles,
            ],
        ]);
    }

    /**
     * Revoke the current access token (logout).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(["message" => "Déconnecté avec succès."]);
    }
}
