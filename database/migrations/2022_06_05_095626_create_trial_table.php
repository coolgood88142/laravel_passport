<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trial', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_name', 30);
            $table->string('user_name', 6);
            $table->string('email')->unique();
            $table->integer('purpose_id');
            $table->string('source', 30);
            $table->string('other_text', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trial');
    }
}
