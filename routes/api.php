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

Route::post($rootV1 . "/volunteers/{id}/roles/{role_name}", [
    VolunteerController::class,
    "addRoleByName",
]);

Route::post($rootV1 . "/volunteers/{id}/roles/{role_id}/add", [
    VolunteerController::class,
    "addRole",
]);

Route::delete($rootV1 . "/volunteers/{id}/roles/{role_id}", [
    VolunteerController::class,
    "removeRole",
]);

Route::put($rootV1 . "/volunteers/{id}/roles", [
    VolunteerController::class,
    "replaceRoles",
]);
Route::get($rootV1 . "/volunteers/{id}/roles", [
    VolunteerController::class,
    "getRoles",
]);
Route::get($rootV1 . "/volunteers/{id}/roles/check/{role_id}", [
    VolunteerController::class,
    "hasRole",
]);
Route::delete($rootV1 . "/volunteers/{id}/roles/{role_name}", [
    VolunteerController::class,
    "removeRoleByName",
]);
