<?php

use App\Enum\Gender;
use App\Enum\UserRole;
use App\Enum\UserStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_code', 20)->unique();
            $table->string('name', 50);
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->unique();
            $table->string('password')->nullable();
            $table->string('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->tinyInteger('gender')->unsigned()->nullable()->default(Gender::OTHER->value);
            $table->unsignedInteger('num_borrowed_books')->default(0);
            $table->string('google_id')->nullable();
            $table->tinyInteger('role')->unsigned()->default(UserRole::USER->value);
            $table->tinyInteger('status')->unsigned()->default(UserStatus::ACTIVE->value);
            $table->foreignId('province_id')->nullable()->constrained('provinces');
            $table->foreignId('district_id')->nullable()->constrained('districts');
            $table->foreignId('ward_id')->nullable()->constrained('wards');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
