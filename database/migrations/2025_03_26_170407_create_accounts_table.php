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
        /**
         * Accounts Table Migration
         * 
         * Commit: Creates the core financial accounts table
         * Stores all banking accounts with their relationships and financial data
         * Designed to support multiple currencies and account types
         */
        Schema::create('accounts', function (Blueprint $table) {
            // Commit: UUID primary key
            // Uses UUID instead of auto-increment for better security and distribution
            // Enables non-sequential account identifiers
            $table->uuid('id')->primary();

            // Commit: User relationship with cascading delete
            // Links to users table with foreign key constraint
            // Automatically deletes accounts when owner is deleted
            $table->foreignUuid('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Commit: Human-readable account identifier
            // Unique name for user-facing identification
            // Enforces uniqueness across all accounts
            $table->string('account_name')->unique();

            // Commit: System-generated account number
            // Large integer field for numeric account IDs
            // Enforces uniqueness with index
            $table->bigInteger('account_number')->unique();

            // Commit: Account classification
            // Restricted to Personal or Business types
            // Ensures consistent account categorization
            $table->enum('account_type', ['Personal', 'Business']);

            // Commit: Currency denomination
            // Supports major world currencies
            // Restricted to valid currency options
            $table->enum('currency', ['USD', 'EUR', 'GBP']);

            // Commit: Current account balance
            // Decimal with precision for financial calculations
            // 15 digits with 2 decimal places (supports up to ~9 trillion)
            $table->decimal('balance', 15, 2)->default(0);

            // Commit: Automatic timestamps
            // Tracks when account was created and last updated
            $table->timestamps();

            // Commit: Soft delete support
            // Allows account deactivation without data loss
            // Preserves financial records while hiding deleted accounts
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
