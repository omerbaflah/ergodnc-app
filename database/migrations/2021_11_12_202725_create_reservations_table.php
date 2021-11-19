<?php

use App\Models\Reservation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->integer('price');
            $table->tinyInteger('status')->default(Reservation::STATUS_ACTIVE);
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('user_id');
            $table->foreignId('office_id');
            $table->timestamps();

            $table->index(['user_id','status']);
            $table->index(['office_id','status']);
            $table->index(['office_id','status','start_date','end_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}
