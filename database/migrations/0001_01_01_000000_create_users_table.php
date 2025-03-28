<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Commit: Primary UUID identifier
            // Uses UUID instead of auto-increment ID for better security and distribution
            $table->uuid('id')->primary();
        
            // Commit: Unique username field
            // Case-sensitive unique constraint for user identification
            $table->string('name')->unique();
        
            // Commit: Unique email address
            // Case-insensitive unique constraint for authentication
            $table->string('email')->unique();
        
            // Commit: Email verification timestamp
            // Tracks when email was verified (nullable for unverified users)
            $table->timestamp('email_verified_at')->nullable();
        
            // Commit: Hashed password storage
            // Stores bcrypt hashed passwords (handled by Laravel's Auth system)
            $table->string('password');
        
            // Commit: "Remember me" token
            // Supports persistent login sessions via remember_token cookie
            $table->rememberToken();
        
            // Commit: Automatic timestamps
            // Tracks created_at and updated_at timestamps automatically
            $table->timestamps();
        
            // Commit: Soft delete support
            // Adds deleted_at column for non-destructive deletion
            $table->softDeletes();
        });
        /**
         * Password Reset Tokens Table
         * 
         * Commit: Stores password reset tokens for user authentication
         * Used by Laravel's password reset functionality
         * Tokens expire after configurable timeframe
         */
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            // Commit: Primary email identifier
            // References user's email for password reset requests
            // Primary key ensures one active token per email
            $table->string('email')->primary();
            
            // Commit: Hashed reset token
            // Secure token used for password reset links
            // Hashed for security before storage
            $table->string('token');
            
            // Commit: Token creation timestamp
            // Used to enforce token expiration
            // Nullable for backward compatibility
            $table->timestamp('created_at')->nullable();
        });

        /**
         * Sessions Table
         * 
         * Commit: Tracks active user sessions
         * Stores session data for authenticated users
         * Supports multiple devices/browsers per user
         */
        Schema::create('sessions', function (Blueprint $table) {
            // Commit: Session ID (primary key)
            // Unique identifier for each session
            // Matches session cookie value
            $table->string('id')->primary();
            
            // Commit: Associated user reference
            // Nullable for guest sessions
            // Indexed for faster user session lookups
            $table->foreignId('user_id')->nullable()->index();
            
            // Commit: Client IP address storage
            // IPv6-capable field (45 characters max)
            // Used for security auditing
            $table->string('ip_address', 45)->nullable();
            
            // Commit: User agent/browser info
            // Stores device/browser identification
            // Helps identify session devices
            $table->text('user_agent')->nullable();
            
            // Commit: Serialized session data
            // Stores encrypted session payload
            // Uses LONGTEXT for complex data
            $table->longText('payload');
            
            // Commit: Last activity timestamp
            // Unix timestamp format
            // Indexed for session garbage collection
            $table->integer('last_activity')->index();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
