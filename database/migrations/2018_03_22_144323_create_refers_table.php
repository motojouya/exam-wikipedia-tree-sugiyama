<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refers', function (Blueprint $table) {
            $table->integer('refer_from');
            $table->integer('refer_to');
            $table->timestamps();
            $table->primary(['refer_from', 'refer_to']);
            $table->foreign('refer_from')
                   ->references('id')
                   ->on('keywords');
            $table->foreign('refer_to')
                   ->references('id')
                   ->on('keywords');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('refers');
    }
}
