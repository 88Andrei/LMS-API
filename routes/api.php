<?php

use App\Http\Controllers\Api\V1\CourseUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/courses/{course}/assign-user', [CourseUserController::class, 'assignUser']);