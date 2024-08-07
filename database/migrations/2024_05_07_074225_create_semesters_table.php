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
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('price');
            $table->date('start_date');
            $table->date('end_date');
            $table->double('period');
            $table->string('unit');
            $table->date('actual_start_date')->nullable();
            $table->date('actual_completion_date')->nullable();
            $table->boolean('status')->default(\App\Status\Semester::waiting);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
