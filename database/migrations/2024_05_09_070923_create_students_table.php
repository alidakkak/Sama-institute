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
            $table->integer('age');
            $table->date('date_of_birth');
            $table->string('place_of_birth');
            $table->string('gender');
            $table->string('marital_status')->nullable();
            $table->string('previous_educational_status');
            $table->string('phone_number')->nullable();
            $table->string('telephone_number')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('location');
            $table->string('father_name')->nullable();
            $table->string('father_work')->nullable();
            $table->date('father_of_birth')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_work')->nullable();
            $table->date('mother_of_birth')->nullable();
            $table->string('other_name')->nullable();
            $table->string('other_work')->nullable();
            $table->date('other_of_birth')->nullable();
            $table->string('note1')->nullable();
            $table->string('note2')->nullable();
            $table->string('image')->nullable();
            $table->string('user_name')->unique();
            $table->string('password');
            $table->foreignId('semester_id')->references('id')
                ->on('semesters')->onDelete('cascade');
            $table->foreignId('scholarship_id')->nullable()->references('id')
                ->on('scholarships')->onDelete('cascade');
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
