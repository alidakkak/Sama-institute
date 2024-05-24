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
            $table->text('name');
            $table->string('user_name')->unique();
            $table->string('password');
            $table->string('birthdate');
            $table->foreignId('grade_id')->references('id')
                ->on('grades')->onDelete('cascade');
            $table->foreignId('academic_year_id')->references('id')
                ->on('academic_years')->onDelete('cascade');
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
