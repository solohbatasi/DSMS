<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('meters', function (Blueprint $table) {
            $table->id();
            $table->string('meter_number')->unique();
            $table->string('meter_type')->default('domestic');
            $table->string('meter_model')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('status')->default('available'); // available, assigned, faulty, maintenance
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('installation_address')->nullable();
            $table->date('installation_date')->nullable();
            $table->date('last_maintenance_date')->nullable();
            $table->decimal('initial_reading', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meters');
    }
};
