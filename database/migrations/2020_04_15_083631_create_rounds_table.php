<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rounds', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('game_id');
            $table->unsignedInteger('round_number')->default(1);
            $table->unsignedInteger('turn_number')->default(1);
            $table->string('phase')->default('role_selection');
            $table->unsignedInteger('players_turn');
            $table->text('deck');
            $table->unsignedInteger('num_cards_in_deck')->default(0);
            $table->text('available_roles')->nullable();
            $table->text('players_with_selected_roles')->nullable();
            $table->text('board')->nullable();
            $table->unsignedInteger('gold_location');
            $table->text('reached_gold_locations')->nullable();
            $table->text('winning_teams')->nullable();
            $table->text('revealed_players')->nullable();
            $table->boolean('current')->default(true);
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
        Schema::dropIfExists('rounds');
    }
}
