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
        Schema::create('trainer_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('employees')->onDelete('cascade');
            $table->unsignedBigInteger('member_id');
            $table->string('schedule');
            $table->text('notes')->nullable();
            $table->string('status')->default('active'); // active, completed, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainer_assignments');
    }
};
