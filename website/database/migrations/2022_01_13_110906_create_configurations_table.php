<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id', 0);
            $table->timestamp('span')->nullable();
            $table->string('day');
            $table->string('data', 20);
            $table->string('wifi');
            $table->string('label1');
            $table->string('mode')->default('attendance');
            $table->string('label2')->nullable();
            $table->string('label3')->nullable();
            $table->string('label4')->nullable();
            // $table->foreignId('user_id')->on('users');
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
        Schema::dropIfExists('configurations');
    }
}
