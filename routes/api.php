<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/**
 * Auth routes
 */
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
});

/**
 * Project's routes
 */
Route::middleware('jwt.verify')->resource('projects', ProjectController::class);

/**
 * Task's routes
 */
Route::middleware('jwt.verify')->resource('tasks', TaskController::class);
Route::middleware('jwt.verify')->controller(TaskController::class)->prefix('tasks')->group(function () {
    Route::get('/user/{idUser}', 'getTasksByUser');
});

/**
 * User's routes
 */
Route::middleware('jwt.verify')->resource('users', UserController::class);
