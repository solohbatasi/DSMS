<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('water_connection_applications', function (Blueprint $table) {
            // Add indexes for frequently searched columns
            $table->index(['first_name', 'last_name']);
            $table->index('email');
            $table->index('phone');
            $table->index('national_id');
            $table->index('plot_number');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::table('water_connection_applications', function (Blueprint $table) {
            $table->dropIndex(['first_name', 'last_name']);
            $table->dropIndex(['email']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['national_id']);
            $table->dropIndex(['plot_number']);
            $table->dropIndex(['status']);
        });
    }
};