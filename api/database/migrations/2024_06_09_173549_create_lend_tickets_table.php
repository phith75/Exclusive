<?php

use App\Enum\LendTicketStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLendTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('lend_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('lend_ticket_code')->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->string('name');
            $table->string('address');
            $table->string('email');
            $table->tinyInteger('status')->unsigned()->default(LendTicketStatus::APPROVED->value);
            $table->date('borrowed_date');
            $table->string('phone', 20);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lend_tickets');
    }
}
