<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostmanEmailLogTable extends Migration
{
    public function up()
    {
        Schema::create('postman_email_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('email_id')->unsigned();
            $table->timestamp('date');
            $table->string('event');
            $table->string('url')->nullable();

            $table->foreign('email_id')->references('id')->on('postman_email');
        });
    }

    public function down()
    {
        Schema::dropIfExists('postman_email_log');
    }
}
