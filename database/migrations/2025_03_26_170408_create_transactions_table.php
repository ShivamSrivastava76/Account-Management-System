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
         * Transactions Table Migration
         * 
         * Commit: Creates ledger for all financial transactions
         * Records monetary movements between accounts with audit trail
         * Maintains immutable record of all financial activities
         */
        Schema::create('transactions', function (Blueprint $table) {
            // Commit: UUID primary key
            // Uses UUID for secure, non-sequential transaction IDs
            // Enables distributed systems to generate IDs without collision
            $table->uuid('id')->primary();

            // Commit: Account relationship
            // Foreign key linking to accounts table
            // No cascade delete to preserve transaction history
            $table->foreignUuid('account_id')
                ->references('id')
                ->on('accounts');

            // Commit: Transaction type classification
            // Strictly limited to Credit (deposit) or Debit (withdrawal)
            // Ensures proper balance calculations
            $table->enum('type', ['Credit', 'Debit']);

            // Commit: Transaction amount
            // Precise decimal storage (15 digits, 2 decimal places)
            // Handles values up to 9 trillion with cent precision
            $table->decimal('amount', 15, 2);

            // Commit: Transaction memo
            // Optional human-readable description
            // TEXT field allows for lengthy transaction notes
            $table->text('description')->nullable();

            // Commit: Automatic timestamps
            // created_at: When transaction was executed
            // updated_at: If any metadata was later modified
            $table->timestamps();

            // Commit: Soft delete support
            // Allows transaction reversal while maintaining audit trail
            // deleted_at marks reversed transactions without physical deletion
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
