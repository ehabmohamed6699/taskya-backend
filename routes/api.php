<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('projects', ProjectController::class);
    Route::get('/projects/{project}/members', [ProjectMemberController::class, 'listMembers']);
    Route::post('/projects/{project}/members', [ProjectMemberController::class, 'addMember']);
    Route::put('/projects/{project}/members/{user}', [ProjectMemberController::class, 'changeMemberRole']);
    Route::delete('/projects/{project}/members/{user}', [ProjectMemberController::class, 'removeMember']);
    Route::post('/comments/{task}',[CommentController::class,'store']);
    Route::put('/comments/{id}',[CommentController::class,'update']);
    Route::delete('/comments/{id}',[CommentController::class,'destroy']);
    Route::post('/tasks/{task}',[TaskController::class, 'toggleCompleted']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
