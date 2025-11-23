<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('practical_sessions')->default(0)->after('training_duration');
            $table->integer('theoretical_sessions')->default(0)->after('practical_sessions');
            $table->integer('total_sessions')->virtualAs('practical_sessions + theoretical_sessions')->after('theoretical_sessions');
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['practical_sessions', 'theoretical_sessions', 'total_sessions']);
        });
    }
};
