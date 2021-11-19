<?php

use App\Models\Office;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->decimal('lat',11,8);
            $table->decimal('lng',11,8);
            $table->text('address_line1');
            $table->text('address_line2')->nullable();
            $table->tinyInteger('approval_status')->default(Office::APPROVAL_APPROVED);
            $table->boolean('hidden')->default(Office::VISIBLE);
            $table->integer('price_per_day');
            $table->integer('monthly_discount')->default(0);
            $table->foreignId('user_id')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offices');
    }
}
