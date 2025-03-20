<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HospedagemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Autenticação
Route::get('login', [AuthController::class, 'showLoginForm']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

// Recuperação de Senha
Route::get('password/reset', [AuthController::class, 'showLinkRequestForm']);
Route::post('password/email', [AuthController::class, 'sendResetLinkEmail']);
Route::get('password/reset/{token}', [AuthController::class, 'showResetForm']);
Route::post('password/reset', [AuthController::class, 'reset']);

// Gerenciamento de Hospedagens
Route::get('hospedagens', [HospedagemController::class, 'index']);
Route::get('hospedagens/create', [HospedagemController::class, 'create']);
Route::post('hospedagens', [HospedagemController::class, 'store']);
Route::get('hospedagens/{id}', [HospedagemController::class, 'show']);
Route::get('hospedagens/{id}/edit', [HospedagemController::class, 'edit']);
Route::put('hospedagens/{id}', [HospedagemController::class, 'update']);
Route::delete('hospedagens/{id}', [HospedagemController::class, 'destroy']);
