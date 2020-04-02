<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('game_master_id');
            $table->string('status')->default('open');
            $table->unsignedInteger('round')->default(1);
            $table->unsignedInteger('player_turn_index')->default(0);
            $table->unsignedInteger('gold_location_index')->default(0);
            $table->text('board')->nullable();
            $table->text('deck')->nullable();
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
        Schema::dropIfExists('games');
    }
}
