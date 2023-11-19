<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unsignedBigInteger('folder_id')->nullable();
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('set null');
            $table->integer('no_mistakes')->default(0);
            $table->integer('no_pages')->default(0);
            $table->integer('time_in_minutes')->nullable();
            $table->boolean('is_special')->default(false);
            $table->integer('mark')->nullable();
            $table->json('pages')->nullable();
            $table->unsignedBigInteger('emoji_id')->nullable();
            $table->foreign('emoji_id')->references('id')->on('emoji')->onDelete('set null');
            $table->date('date')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
