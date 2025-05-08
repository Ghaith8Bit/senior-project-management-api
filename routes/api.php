<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SupervisorController;
use Illuminate\Http\Request;
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

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::prefix('students/projects')->middleware(['auth:sanctum', 'role:student'])->group(function () {
    Route::post('/suggest', [StudentController::class, 'suggestProject']);
    Route::get('/', [StudentController::class, 'viewProject']);
    Route::get('/my/notes', [StudentController::class, 'viewNotes']);
    Route::post('/my/update', [StudentController::class, 'submitProgressUpdate']);
    Route::get('/my/updates', [StudentController::class, 'viewProgressUpdates']);
});

Route::middleware(['auth:sanctum', 'role:supervisor'])->prefix('supervisor/projects')->group(function () {
    Route::get('/', [SupervisorController::class, 'myProjects']);
    Route::get('/{id}', [SupervisorController::class, 'showProject']);
    Route::post('/{id}/notes', [SupervisorController::class, 'addNote']);
    Route::post('/{id}/rate', [SupervisorController::class, 'rateProgress']);
});

Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::prefix('suggestions')->group(function () {
        Route::get('/', [AdminController::class, 'listSuggestions']);
        Route::post('/{suggestion}/accept', [AdminController::class, 'acceptSuggestion']);
        Route::post('/{suggestion}/reject', [AdminController::class, 'rejectSuggestion']);
    });

    Route::prefix('projects')->group(function () {
        Route::post('/{project}/assign-supervisor', [AdminController::class, 'assignSupervisor']);
        Route::post('/{project}/final-rating', [AdminController::class, 'setFinalRating']);
        Route::get('/', [AdminController::class, 'listProjects']);
        Route::get('/{project}', [AdminController::class, 'viewProject']);
    });


    Route::get('/supervisors', [AdminController::class, 'listSupervisors']);
    Route::get('/students', [AdminController::class, 'listStudents']);
    Route::post('/register', [AdminController::class, 'register']);
});
