<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#FFC0CB');
            $table->string('photo')->nullable();
            $table->foreignId('branch_id')->constrained();
            $table->string('key')->unique();
            $table->integer('previous_consistency')->default(0);
            $table->integer('current_consistency')->default(0);
            $table->integer('max_consistency')->default(0);
            $table->foreignId('current_folder_id')->nullable()->constrained('folders')->onDelete('set null')->default(null);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
}
