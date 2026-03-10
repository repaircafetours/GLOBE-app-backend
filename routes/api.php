<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\VolunteerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get("/v1/visitors/{visitor}", [VisitorController::class, 'show']);
Route::get("/v1/visitors", [VisitorController::class, 'index']);
Route::post("/v1/visitors", [VisitorController::class, 'store']);
Route::patch("/v1/visitors/{visitor}", [VisitorController::class, 'update']);
Route::delete("/v1/visitors/{visitor}", [VisitorController::class, 'destroy']);


Route::get("/v1/visitors/{visitor}/items", [ItemController::class, 'index']);
Route::get("/v1/visitors/{visitor}/items/{item}", [ItemController::class, 'show']);
Route::post("/v1/visitors/{visitor}/items", [ItemController::class, 'store']);
Route::get("/v1/items/{item}", [ItemController::class, 'showById']);
Route::patch("/v1/items/{item}", [ItemController::class, 'update']);


Route::get('/volunteer', [VolunteerController::class, 'index']);

Route::get('/volunteer/{id}', [VolunteerController::class, 'show']);
