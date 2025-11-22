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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ef_id')->constrained('student_ef_lists')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->text('remarks')->nullable();
            $table->string('payment_status')->default('pending'); // pending, completed, failed
            $table->string('payment_method')->nullable(); // e.g., MPESA, Cash, Bank
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
