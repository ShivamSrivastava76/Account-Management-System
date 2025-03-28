<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Transaction extends Model
{
    use HasApiTokens, SoftDeletes;

    protected $keyType = 'uuid';
    public $incrementing = false;
    
    /**
     * The attributes that are mass assignable.
     * 
     * Commit: Define fillable fields for transaction creation
     * Safely allows mass assignment for these specific fields only
     * Protects against unintended field modifications
     * 
     * @var array<string>
     */
    protected $fillable = [
        'id',            // UUID primary key for the transaction
        'account_id',    // Reference to associated account (foreign key)
        'type',          // Transaction type (Credit/Debit)
        'amount',        // Monetary value of the transaction
        'description'    // Optional transaction memo
    ];

    /**
     * The attributes that should be cast.
     * 
     * Commit: Ensure proper monetary amount handling
     * Casts amount to decimal with 2 places for financial precision
     * Prevents floating point rounding errors in calculations
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2' // Store currency values with exact precision
    ];

    /**
     * Account relationship
     * 
     * Commit: Define transaction's account association
     * Establishes inverse of Account's transactions relationship
     * Enables eager loading and querying of related account
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
