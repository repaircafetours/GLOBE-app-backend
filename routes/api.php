<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\VolunteerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$rootV1 = "/v1";

Route::get("/user", function (Request $request) {
    return $request->user();
})->middleware("auth:sanctum");

Route::get($rootV1 . "/visitors/{visitor}", [VisitorController::class, "show"]);
Route::get($rootV1 . "/visitors", [VisitorController::class, "index"]);
Route::post($rootV1 . "/visitors", [VisitorController::class, "store"]);
Route::patch($rootV1 . "/visitors/{visitor}", [
    VisitorController::class,
    "update",
]);
Route::delete($rootV1 . "/visitors/{visitor}", [
    VisitorController::class,
    "destroy",
]);
Route::get($rootV1 . "/volunteers", [VolunteerController::class, "index"]);
Route::get($rootV1 . "/volunteers/{volunteer}", [
    VolunteerController::class,
    "show",
]);
Route::post($rootV1 . "/volunteers", [VolunteerController::class, "store"]);
Route::patch($rootV1 . "/volunteers/{volunteer}", [
    VolunteerController::class,
    "update",
]);
Route::delete($rootV1 . "/volunteers/{volunteer}", [
    VolunteerController::class,
    "destroy",
]);

Route::post($rootV1 . "/volunteers/{volunteer}/roles/{role}/add", [
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
Route::get($rootV1 . "/volunteers/{volunteer}/roles", [
    VolunteerController::class,
    "getRoles",
]);
Route::get($rootV1 . "/volunteers/{volunteer}/roles/check/{role}", [
    VolunteerController::class,
    "hasRole",
]);
