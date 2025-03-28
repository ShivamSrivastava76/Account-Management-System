<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateAccountRequest extends FormRequest
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
     * Validation rules for partial account updates
     * 
     * Commit: Define flexible validation for PATCH/PUT updates
     * Allows selective field updates while maintaining data integrity
     */
    public function rules(): array
    {
        return [
            // Commit: Validate email format and uniqueness when provided
            // Allows email updates but prevents duplicates
            // Note: Nullable for partial updates
            'email' => ['nullable', 'email', 'unique:users,email'],
            
            // Commit: Validate password strength when provided
            // Enforces minimum length if password is being updated
            // Nullable to allow updating other fields without changing password
            'password' => ['nullable', 'string', 'min:8'],
            
            // Commit: Validate account name uniqueness when provided
            // Allows name changes while preventing duplicates
            // Max length matches database schema
            'account_name' => ['nullable', 'string', 'max:255', 'unique:accounts,account_name'],
            
            // Commit: Validate account type against allowed values
            // Only accepts predefined business/personal types
            // Nullable to allow updating other fields independently
            'account_type' => ['nullable', 'in:Personal,Business'],
            
            // Commit: Validate currency against supported types
            // Restricts to predefined currency options
            // Nullable for partial updates
            'currency' => ['nullable', 'in:USD,EUR,GBP,JPY,AUD'],
        ];
    }

    /**
     * Handle failed validation attempts
     * 
     * Commit: Standardize validation error responses
     * Provides consistent API error format for client applications
     */
    protected function failedValidation(Validator $validator)
    {
        // Commit: Return structured JSON error response
        // Includes success flag, general message, and detailed field errors
        // Uses 422 status for validation failures (Unprocessable Entity)
        throw new HttpResponseException(
            response()->json([
                'success' => false,               // Standard failure indicator
                'message' => 'Validation errors',  // General error context
                'errors' => $validator->errors()   // Detailed field-level errors
            ], 422)
        );
    }
}
