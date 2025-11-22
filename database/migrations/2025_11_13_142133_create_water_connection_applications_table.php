<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('water_connection_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('kra_pin');
            $table->string('kra_pin_file')->nullable();
            $table->string('national_id');
            $table->string('national_id_file')->nullable();
            $table->string('plot_number');
            $table->string('house_number');
            $table->string('estate')->nullable();
            $table->integer('expected_users')->nullable();
            $table->string('property_owner');
            $table->string('title_document')->nullable();
            $table->string('signature')->nullable();
            $table->date('date');
            $table->enum('status', ['pending', 'approved', 'declined'])->default('pending');
            $table->text('decline_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('water_connection_applications');
    }
};
