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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->string('contract_number')->unique();
            $table->text('terms_and_conditions');
            $table->json('equipment_condition_before')->nullable();
            $table->json('equipment_condition_after')->nullable();
            $table->string('borrower_signature')->nullable();
            $table->string('lender_signature')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('status')->default('draft'); // draft, signed, completed, cancelled
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
