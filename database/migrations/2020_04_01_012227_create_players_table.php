<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('game_id');
            $table->unsignedInteger('role_id')->nullable();
            $table->unsignedInteger('player_number');
            $table->unsignedInteger('score')->default(0);
            $table->text('hand')->nullable();
            $table->boolean('cart_available')->default(true);
            $table->boolean('light_available')->default(true);
            $table->boolean('pickaxe_available')->default(true);
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
        Schema::dropIfExists('players');
    }
}
