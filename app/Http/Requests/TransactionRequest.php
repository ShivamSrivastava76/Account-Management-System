<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    /**
     * Transaction request validation rules
     * 
     * Commit: Define validation rules for financial transactions
     * Ensures all transactions meet business and security requirements
     */
    public function rules(): array
    {
        return [
            // Commit: Validate account existence and ownership
            // Ensures transactions only occur on valid, accessible accounts
            // Note: Additional ownership check happens in controller
            'account_id' => ['required', 'exists:accounts,id'],
            
            // Commit: Restrict transaction types to valid options
            // Maintains financial system integrity by only allowing Credit/Debit
            'type' => ['required', 'in:Credit,Debit'],
            
            // Commit: Validate transaction amount
            // Ensures positive numeric values to prevent invalid transactions
            'amount' => ['required', 'numeric', 'gt:0'],
            
            // Commit: Validate optional description
            // Allows for transaction memos with reasonable length limit
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Handle failed validation attempts
     * 
     * Commit: Customize validation error response for API consistency
     * Provides clear error structure for client applications
     */
    protected function failedValidation(Validator $validator)
    {
        // Commit: Return standardized JSON error response
        // Includes all validation errors with proper HTTP status code
        throw new HttpResponseException(
            response()->json([
                'success' => false,            // Flag for easy client-side checking
                'message' => 'Validation errors', // General error message
                'errors' => $validator->errors()  // Detailed field-specific errors
            ], 422) // HTTP 422 Unprocessable Entity status
        );
    }

}
