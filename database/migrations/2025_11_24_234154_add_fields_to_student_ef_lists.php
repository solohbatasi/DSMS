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
            // Practical sessions tracking
            $table->integer('attended_practical')->default(0)->after('total_class');
            $table->integer('remaining_practical')->default(0)->after('attended_practical');

            // Theoretical sessions tracking
            $table->integer('attended_theoretical')->default(0)->after('remaining_practical');
            $table->integer('remaining_theoretical')->default(0)->after('attended_theoretical');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_ef_lists', function (Blueprint $table) {
            $table->dropColumn([
                'attended_practical',
                'remaining_practical',
                'attended_theoretical',
                'remaining_theoretical',
            ]);
        });
    }
};
