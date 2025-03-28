<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\PdfController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


/**
 * Account Creation Route
 * 
 * Commit: Public endpoint for new account registration
 * Allows unauthenticated users to create accounts
 * Uses AccountController@store for account setup
 */
Route::post('/accounts', [AccountController::class, 'store']);

/**
 * Authenticated API Routes
 * 
 * Commit: Protected routes with dual security layers
 * Sanctum authentication + rate limiting
 * Groups related financial endpoints together
 */
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    
    /**
     * Account Management Routes
     * 
     * Commit: CRUD operations for financial accounts
     * All routes require account ownership verification
     */
    Route::get('/accounts/{id}', [AccountController::class, 'show']);    // View account
    Route::put('/accounts/{id}', [AccountController::class, 'update']);  // Modify account
    Route::delete('/accounts/{id}', [AccountController::class, 'destroy']); // Close account

    /**
     * Transaction Routes
     * 
     * Commit: Financial transaction processing
     * Requires valid authenticated session
     */
    Route::get('transactions', [TransactionController::class, 'index']);  // List transactions
    Route::post('transactions', [TransactionController::class, 'store']); // Create transaction

    Route::get('/accounts/{id}/statement', [PdfController::class, 'generateStatement']);// transactions in PDF

    Route::post('/transfer', [TransactionController::class, 'transferFunds']); // Fund Transfers
});
