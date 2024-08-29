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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->integer('age')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('gender')->default('female');
            $table->string('marital_status')->nullable();
            $table->string('previous_educational_status')->nullable();
            $table->string('phone_number');
            $table->string('student_phone_number')->nullable();
            $table->string('telephone_number')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('location')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_work')->nullable();
            $table->date('father_of_birth')->nullable();
            $table->string('father_Healthy')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_work')->nullable();
            $table->date('mother_of_birth')->nullable();
            $table->string('mother_Healthy')->nullable();
            $table->string('other_name')->nullable();
            $table->string('other_work')->nullable();
            $table->date('other_of_birth')->nullable();
            $table->string('other_Healthy')->nullable();
            $table->string('note')->nullable();
            $table->string('image')->default('/default_image/female.jpg');
            $table->string('password');
            $table->unsignedInteger('device_user_id')->nullable();
            $table->boolean('is_image_synced')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
