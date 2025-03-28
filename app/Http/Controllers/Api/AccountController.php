<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  App\Models\Account;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use Illuminate\Http\JsonResponse;
use  App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{

    public function __construct()
    {
        $this->user = Auth::user();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAccountRequest $request)
    {
        try {

            // Commit: Generate UUID for new user to ensure unique identifier
            $user_id = Str::uuid()->toString();

            // Commit: Create user record with validated request data
            $user = User::create([
                'id' => $user_id, // Explicitly setting UUID for user
                'name' => $request->account_name, // Using account name as user name
                'email' => $request->email, // Email from request
                'password' => Hash::make($request->password), // Securely hashing the password
            ]);

            // Commit: Generate UUID for account to maintain data integrity
            $account_id = Str::uuid()->toString();
            
            // Commit: Create account record linked to the user
            $account = Account::create([
                'id' => $account_id, // Explicitly setting UUID for account
                'user_id' => $user_id, // Linking account to the newly created user
                'account_name' => $request->account_name, // Account name from request
                'account_number' => generateLuhnAccountNumber(), // Generate a Luhn-compliant account number
                'account_type' => $request->account_type, // Account type (Personal/Business)
                'currency' => $request->currency, // Currency type (USD, EUR, GBP, etc.)
                'balance' => $request->initial_balance ?? 0, // Set initial balance, defaulting to 0
            ]);

            // Commit: Retrieve fresh user instance to ensure data consistency
            $user = User::where('id',$user_id)->first();
            
            // Commit: Generate API token for immediate authentication
            $token = $user->createToken('account-api-token')->plainTextToken;

            // Commit: Return success response with all created resources
            return response()->json([
                'message' => 'Account created successfully',
                'user' => $user,
                'account' => $account,
                'token' => $token,
            ], 201);

        } 
        catch (Exception $e) {
            // Commit: Handle errors gracefully with appropriate response
            return response()->json([
                'error' => 'Account creation failed',
                'message' => $e->getMessage() // Return the actual error message for debugging
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */

    public function show($id): JsonResponse
    {
        try {
            // Commit: Retrieve account that belongs to authenticated user with matching account number
            // Ensures users can only access their own accounts
            $account = Account::where('user_id', $this->user->id)
                            ->where('account_number', $id)
                            ->first();
    
            // Commit: Handle case when account doesn't exist or isn't owned by user
            // Returns consistent 404 error without revealing existence of unauthorized accounts
            if (!$account) {
                return response()->json([
                    'message' => 'Account not found or you do not have permission to access it'
                ], 404);
            }
    
            // Commit: Return account details as JSON response
            // Only returns account data after successful authorization check
            return response()->json($account);
    
        } catch (\Exception $e) {
            // Commit: Handle unexpected errors during account retrieval
            // Logs the error and returns a generic 500 response to client
            \Log::error("Failed to fetch account: " . $e->getMessage());
            
            return response()->json([
                'message' => 'An error occurred while retrieving account information',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccountRequest $request, $id): JsonResponse
    {
        try {
            // Commit: Retrieve the account with authorization check
            // Uses firstOrFail() to automatically throw 404 if account doesn't belong to user
            $account = Account::where('user_id', $this->user->id)
                            ->where('account_number', $id)
                            ->firstOrFail();
    
            // Commit: Update account with validated request data
            // Only updates fields that are marked as fillable in the model
            $account->update($request->validated());
    
            // Commit: Return success response with updated account data
            // Provides clear success feedback to the client
            return response()->json([
                'success' => true,
                'data' => $account,
                'message' => 'Account updated successfully'
            ]);
    
        } catch (ModelNotFoundException $e) {
            // Commit: Handle case when account doesn't exist or isn't owned by user
            // Returns consistent 404 error without exposing sensitive information
            return response()->json([
                'success' => false,
                'message' => 'Account not found or you do not have permission to access it'
            ], 404);
    
        } catch (\Exception $e) {
            // Commit: Handle unexpected errors during account update
            // Logs the error for debugging while returning user-friendly message
            \Log::error("Account update failed: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update account',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {

            // Commit: Find account with authorization check
            // Ensures users can only delete their own accounts
            $account = Account::where('user_id', $this->user->id)->where('account_number', $id)->first();

            // Commit: Handle non-existent or unauthorized account access
            // Returns consistent 404 response for security
            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account not found or you do not have permission to access it'
                ], 404);
            }

            // Commit: Delete the account record
            // Performs actual deletion operation
            $account->delete();

            // Commit: Return empty success response (204 No Content)
            // Follows REST conventions for delete operations
            return response()->json(null, 204);

        } catch (\Exception $e) {
            // Commit: Handle unexpected errors during deletion
            // Logs error while returning user-friendly message
            \Log::error("Account deletion failed - Account: {$id}, Error: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete account',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
