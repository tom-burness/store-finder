<?php

use App\Http\Controllers\StoreController;
use App\Http\Controllers\StoreDeliverableController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/stores', [StoreController::class, 'store'])->middleware(['auth:sanctum']);
Route::get('/stores', [StoreController::class, 'index'])->middleware(['auth:sanctum']);
Route::get('/stores/deliverable', StoreDeliverableController::class)->middleware(['auth:sanctum']);
