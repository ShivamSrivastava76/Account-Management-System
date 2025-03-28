<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TransferRequest extends FormRequest
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
    
     public function rules(): array
     {
         return [
             'from_account_number' => ['required', 'exists:accounts,id'],
             'to_account_number' => ['required', 'exists:accounts,id', 'different:from_account_number'],
             'amount' => ['required', 'numeric', 'gt:0'],
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
