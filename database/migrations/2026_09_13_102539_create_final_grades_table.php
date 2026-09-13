<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('final_grades', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                ->constrained('courses');

            $table->foreignId('user_id')
                ->constrained('users');

            $table->decimal('total_score', 5, 2);

            $table->string('letter_grade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('final_grades');
    }
};