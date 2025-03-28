<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Account extends Model
{
    use HasApiTokens, SoftDeletes;

    protected $keyType = 'uuid';
    public $incrementing = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',               // UUID primary key
        'user_id',          // Owner relationship
        'account_name',     // Human-readable identifier
        'account_number',   // System-generated account number
        'account_type',     // Personal/Business classification
        'currency',         // Currency denomination
        'balance',          // Current account balance
    ];
    
    protected $casts = [
        'balance' => 'decimal:2', // Store with 2 decimal places precision
    ];

    /**
     * User relationship
     * 
     * Commit: Define account ownership relationship
     * Each account belongs to a single user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Transactions relationship
     * 
     * Commit: Define account transaction history
     * An account can have many transactions
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

}
