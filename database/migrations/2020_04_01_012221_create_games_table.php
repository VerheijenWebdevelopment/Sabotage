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
            $table->unsignedInteger('turn')->default(1);
            $table->string('phase')->default('role_selection');
            $table->unsignedInteger('player_turn')->default(1);
            $table->unsignedInteger('gold_location')->default(0);
            $table->text('deck')->nullable();
            $table->unsignedInteger('num_cards_in_deck')->default(0);
            $table->text('roles')->nullable();
            $table->text('available_roles')->nullable();
            $table->text('players_with_selected_roles')->nullable();
            $table->text('board')->nullable();
            $table->text('reached_gold_locations')->nullable();
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
