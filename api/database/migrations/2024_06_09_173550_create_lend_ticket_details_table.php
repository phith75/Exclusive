<?php

use App\Enum\TicketDetailStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLendTicketDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('lend_ticket_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lend_ticket_id')->constrained('lend_tickets');
            $table->foreignId('book_id')->constrained('books');
            $table->string('book_name');
            $table->unsignedInteger('quantity')->default(1);
            $table->tinyInteger('status')->unsigned()->default(TicketDetailStatus::BORROWED->value);
            $table->date('expected_refund_time');
            $table->date('returned_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lend_ticket_details');
    }
}
