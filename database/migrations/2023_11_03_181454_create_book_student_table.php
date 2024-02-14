<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('book_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('version_id')->constrained('versions')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->boolean('is_open')->default(true);
            $table->integer('percentage_finished')->default(0);
            $table->binary('assigned_finished')->default("\x00");
            $table->timestamps();
            $table->unique(['student_id', 'version_id']);

        });
    }
    public function down(): void
    {
        Schema::dropIfExists('book_students');
    }
};
