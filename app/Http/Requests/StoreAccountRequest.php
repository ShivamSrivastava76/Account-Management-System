<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAccountRequest extends FormRequest
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
     * Define the validation rules for account creation.
     * 
     * Commit: Establish comprehensive validation rules
     * Ensures data integrity and security for new accounts
     */
    public function rules(): array
    {
        return [
            // Commit: Validate email format and uniqueness
            // Prevents duplicate accounts and ensures valid email format
            'email' => ['required', 'email', 'unique:users,email'],
            
            // Commit: Enforce strong password requirements
            // Minimum 8 characters to enhance account security
            'password' => ['required', 'string', 'min:8'],
            
            // Commit: Validate account name with uniqueness check
            // Ensures account names are distinct and properly formatted
            'account_name' => ['required', 'string', 'max:255', 'unique:accounts,account_name'],
            
            // Commit: Restrict account type to predefined values
            // Maintains data consistency for account classification
            'account_type' => ['required', 'in:Personal,Business'],
            
            // Commit: Validate supported currencies
            // Limits to predefined currency options for financial consistency
            'currency' => ['required', 'in:USD,EUR,GBP,JPY,AUD'],
            
            // Commit: Validate optional initial balance
            // Ensures non-negative values if provided
            'initial_balance' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Handle failed validation attempts.
     * 
     * Commit: Customize validation error response
     * Provides structured error feedback in API format
     */
    protected function failedValidation(Validator $validator)
    {
        // Commit: Return consistent JSON error format
        // Includes all validation errors with proper HTTP status
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
