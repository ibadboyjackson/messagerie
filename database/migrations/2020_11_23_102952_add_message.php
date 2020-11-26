<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('from_id')->unsigned();
            $table->bigInteger('to_id')->unsigned();

            $table->foreign('from_id', 'from')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('to_id', 'to')->references('id')->on('users')->onDelete('cascade');

            $table->longText('content');
            $table->timestamp('created_at')->useCurrent();
            $table->dateTime('read_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
