<?php

use App\Enum\ReviewStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books');
            $table->foreignId('user_id')->constrained('users');
            $table->string('user_name')->constrained('users', 'name');
            $table->string('book_name')->constrained('books', 'name');
            $table->text('description');
            $table->tinyInteger('rating')->unsigned()->default(0);
            $table->tinyInteger('status')->unsigned()->default(ReviewStatus::INACTIVE->value);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
