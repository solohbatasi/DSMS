<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {

            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->unsignedBigInteger('category_id')->nullable()->after('user_id');

            $table->string('reference')->nullable()->after('category_id');
            $table->string('vendor')->nullable()->after('reference');

            $table->date('expense_date')->nullable()->after('vendor');
            $table->string('payment_method')->nullable()->after('expense_date');

            $table->string('attachment')->nullable()->after('payment_method');
            $table->string('status')->default('approved')->after('attachment');

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('category_id')->references('id')->on('expense_categories')->nullOnDelete();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            //
        });
    }
};
