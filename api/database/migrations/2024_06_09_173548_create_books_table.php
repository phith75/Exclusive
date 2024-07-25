<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('thumbnail')->nullable();
            $table->string('slug')->unique();
            $table->foreignId('author_id')->constrained('authors');
            $table->foreignId('publisher_id')->constrained('publishers');
            $table->foreignId('category_id')->constrained('categories');
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->unsignedInteger('borrowed_count')->default(0);
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('books');
    }
}
