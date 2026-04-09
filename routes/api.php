<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\VolunteerController;
use Illuminate\Support\Facades\Route;

$rootV1 = "/v1";

// ---------------------------------------------------------------------------
// Auth (public)
// ---------------------------------------------------------------------------
Route::post($rootV1 . "/auth/login", [AuthController::class, "login"]);

// ---------------------------------------------------------------------------
// Visitors – public registration
// ---------------------------------------------------------------------------
Route::post($rootV1 . "/visitors", [VisitorController::class, "store"]);

// Visitor edit token generation
Route::post($rootV1 . "/visitors/token", [
    VisitorController::class,
    "generateToken",
]);

// ---------------------------------------------------------------------------
// Visitors – update via visitor edit token OR Sanctum role:1,3
// ---------------------------------------------------------------------------
Route::patch($rootV1 . "/visitors/{visitor}", [
    VisitorController::class,
    "update",
])->middleware("authorize.visitor.update");

// ---------------------------------------------------------------------------
// Authenticated routes (any valid volunteer token)
// ---------------------------------------------------------------------------
Route::middleware("auth:sanctum")->group(function () use ($rootV1) {
    // Auth
    Route::post($rootV1 . "/auth/logout", [AuthController::class, "logout"]);

    Route::get("/v1/events", [EventController::class, 'index']);
    Route::post("/v1/events", [EventController::class, 'store']);
    Route::get("/v1/events/{event}", [EventController::class, 'show']);
    Route::patch("/v1/events/{event}", [EventController::class, 'update']);
    Route::delete("/v1/events/{event}", [EventController::class, 'destroy']);
    Route::post("/v1/events/{event}/appointments/{item}", [EventController::class, 'addNewItemToEvent']);
    Route::get("/v1/events/{event}/appointments", [EventController::class, "getAppointmentsFromEvent"]);
    Route::patch("/v1/events/{event}/appointments/{item}", [EventController::class, "updateAppointment"]);

    // Read a single visitor or volunteer (any authenticated volunteer)
    Route::get($rootV1 . "/visitors/{visitor}", [
        VisitorController::class,
        "show",
    ]);
    Route::get($rootV1 . "/volunteers/{volunteer}", [
        VolunteerController::class,
        "show",
    ]);
    Route::get($rootV1 . "/volunteers/{volunteer}/roles", [
        VolunteerController::class,
        "getRoles",
    ]);
    Route::get($rootV1 . "/volunteers/{volunteer}/roles/check/{role}", [
        VolunteerController::class,
        "hasRole",
    ]);

    // -----------------------------------------------------------------------
    // Opérationnel (3) or Administrateur (1)
    // -----------------------------------------------------------------------
    Route::middleware("role:1,3")->group(function () use ($rootV1) {
        // Visitors
        Route::get($rootV1 . "/visitors", [VisitorController::class, "index"]);
        Route::delete($rootV1 . "/visitors/{visitor}", [
            VisitorController::class,
            "destroy",
        ]);

        // Volunteers
        Route::get($rootV1 . "/volunteers", [
            VolunteerController::class,
            "index",
        ]);
        Route::patch($rootV1 . "/volunteers/{volunteer}", [
            VolunteerController::class,
            "update",
        ]);

        // Items (nested under visitor)
        Route::get($rootV1 . "/visitors/{visitor}/items", [
            ItemController::class,
            "index",
        ]);
        Route::get($rootV1 . "/visitors/{visitor}/items/{item}", [
            ItemController::class,
            "show",
        ]);
        Route::get($rootV1 . "/items/{item}", [
            ItemController::class,
            "showById",
        ]);
        Route::post($rootV1 . "/visitors/{visitor}/items", [
            ItemController::class,
            "store",
        ]);
        Route::patch($rootV1 . "/items/{item}", [
            ItemController::class,
            "update",
        ]);
        Route::delete($rootV1 . "/items/{item}", [
            ItemController::class,
            "destroy",
        ]);
    });

    // -----------------------------------------------------------------------
    // Administrateur (1) only
    // -----------------------------------------------------------------------
    Route::middleware("role:1")->group(function () use ($rootV1) {
        // Volunteers – create / delete
        Route::post($rootV1 . "/volunteers", [
            VolunteerController::class,
            "store",
        ]);
        Route::delete($rootV1 . "/volunteers/{volunteer}", [
            VolunteerController::class,
            "destroy",
        ]);

        // Role management
        Route::post($rootV1 . "/volunteers/{volunteer}/roles/{role}", [
            VolunteerController::class,
            "addRole",
        ]);
        Route::delete($rootV1 . "/volunteers/{volunteer}/roles/{role}", [
            VolunteerController::class,
            "removeRole",
        ]);
        Route::put($rootV1 . "/volunteers/{volunteer}/roles", [
            VolunteerController::class,
            "replaceRoles",
        ]);
    });
});
