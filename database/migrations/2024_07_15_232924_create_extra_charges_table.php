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
        Schema::create('extra_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreignId('semester_id')->references('id')->on('semesters')->onDelete('cascade');
            $table->text('title');
            $table->text('description')->nullable();
            $table->double('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extra_charges');
    }
};
