<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostmanEmailTable extends Migration
{
    public function up()
    {
        Schema::create('postman_email', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->unique();
            $table->string('from');
            $table->string('to');
            $table->string('subject');
            $table->timestamp('date');
            $table->string('vendor')->nullable();
            $table->string('vendor_response')->nullable();
            $table->string('vendor_status')->nullable();
            $table->string('vendor_message_id')->nullable();
            $table->string('vendor_error')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('postman_email');
    }
}
