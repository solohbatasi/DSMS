<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('id_number');
            $table->text('physical_address');
            $table->string('plot_number');
            $table->string('house_number');
            $table->string('estate')->nullable();
            $table->string('meter_number')->unique()->nullable();
            $table->string('meter_type')->default('domestic');
            $table->string('connection_type')->default('residential');
            $table->decimal('initial_meter_reading', 10, 2)->default(0);
            $table->date('connection_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending', 'suspended'])->default('pending');
            $table->string('kra_pin')->nullable();
            $table->string('property_owner');
            $table->integer('expected_users')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};