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
        Schema::table('student_ef_lists', function (Blueprint $table) {
            $table->decimal('paid_amount', 12, 2)->default(0)->after('total_fee');
            $table->decimal('balance', 12, 2)->default(0)->after('paid_amount');
            $table->date('last_payment_date')->nullable()->after('balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_ef_lists', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'balance', 'last_payment_date']);
        });
    }
};
