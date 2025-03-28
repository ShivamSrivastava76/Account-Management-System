<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TransactionRequest;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->user = Auth::user();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Commit: Initialize query with user authorization
            // Ensures users can only see transactions from their own accounts
            $query = Transaction::query()
                ->whereHas('account', function($q) {
                    $q->where('user_id', auth()->id());
                });

            // Commit: Apply account_id filter if present
            // Allows filtering transactions by specific account
            if ($request->has('account_id')) {
                $query->where('account_id', $request->account_id);
            }

            // Commit: Apply date range filter (from)
            // Filters transactions starting from specified date
            if ($request->has('from')) {
                $query->where('created_at', '>=', $request->from);
            }

            // Commit: Apply date range filter (to)
            // Filters transactions up to specified date
            if ($request->has('to')) {
                $query->where('created_at', '<=', $request->to);
            }

            // Commit: Return paginated results
            // Uses Laravel's pagination for better performance with large datasets
            return response()->json($query->paginate(15));

        } catch (\Exception $e) {
            // Commit: Handle unexpected errors during transaction listing
            // Logs error details while returning user-friendly message
            \Log::error("Transaction listing failed: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve transactions',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionRequest $request): JsonResponse
    {
        try {

            // Commit: Verify account ownership and existence
            // Ensures users can only transact on their own accounts
            $account = Account::where('user_id', $this->user->id)->where('id', $request->account_id)->firstOrFail();

            // Commit: Validate sufficient funds for debit transactions
            // Prevents overdrafts and maintains financial integrity

            if ($request->type === 'Debit' && $account->balance < $request->amount) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient funds for this transaction'
                ], 422);
            }

            // Commit: Update account balance atomically
            // Handles both credit and debit operations
            $account->balance = $request->type === 'Credit' 
                ? $account->balance + $request->amount 
                : $account->balance - $request->amount;
            
            $account->save();

            // Commit: Create transaction record
            // Uses UUID for transaction ID and tracks all details
            $transaction = Transaction::create([
                'id' => Str::uuid()->toString(),
                'account_id' => $request->account_id,
                'type' => $request->type,
                'amount' => $request->amount,
                'description' => $request->description ?? '',
                'balance_after' => $newBalance // Track balance after transaction
            ]);

            // Commit: Return transaction details with success status
            // Provides complete record of the executed transaction
            return response()->json([
                'success' => true,
                'data' => $transaction,
                'message' => 'Transaction completed successfully'
            ], 201);

        } catch (ModelNotFoundException $e) {
        
            return response()->json([
                'success' => false,
                'message' => 'Account not found or access denied'
            ], 404);

        } catch (\Exception $e) {
           
            Log::error("Transaction failed: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Transaction processing failed',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
