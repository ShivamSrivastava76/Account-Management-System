<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    /**
     * Mass assignable attributes
     * 
     * Commit: Defines safely fillable user attributes
     * Whitelists fields that can be set through mass assignment
     * Protects sensitive fields from being unexpectedly modified
     * 
     * @var array<string>
     */
    protected $fillable = [
        'id',        // UUID primary key - manually assignable for system-generated IDs
        'name',      // User's display name - publicly visible identifier
        'email',     // Login credential - must be unique across system
        'password',  // Hashed authentication secret - automatically encrypted
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

}
