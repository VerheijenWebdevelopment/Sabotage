<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('type');                             // Action, Tunnel
            $table->string('name');                             // Name of the card
            $table->string('text')->nullable();                 // Text to write on the card
            $table->text('description')->nullable();            // Description of what the card does
            $table->string('action')->nullable();               // The action this card performs
            $table->text('open_positions')->nullable();         // Directions in which this tunnel card can be connected [top, right, bottom, left]
            $table->boolean('has_ladder')->default(false);      // Tunnel --> does it have a ladder?
            $table->string('ladder_location')->nullable();      // Tunnel with ladder --> tile ladder is placed on (center, top, right, bottom, left)
            $table->boolean('has_crystal')->default(false);     // Tunnel --> does it have a crystal?
            $table->string('crystal_location')->nullable();     // Tunnel with crystal --> tile crystal is placed on (center, top, right, bottom, left)
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
        Schema::dropIfExists('cards');
    }
}
