<?php

use App\Models\Card;
use Illuminate\Database\Seeder;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("cards")->delete();

        // ------------------------------------------------
        // Action cards
        // ------------------------------------------------

        // Sabotage cards
        $action_sabotage_pickaxe = Card::create([
            "type" => "action",
            "name" => "sabotage_pickaxe",
        ]);
        $action_sabotage_light = Card::create([
            "type" => "action",
            "name" => "sabotage_light",
        ]);
        $action_sabotage_cart = Card::create([
            "type" => "action",
            "name" => "sabotage_cart"
        ]);

        // Sabotage combo cards
        $action_sabotage_pickaxe_light = Card::create([
            "type" => "action",
            "name" => "sabotage_pickaxe_light",
        ]);
        $action_sabotage_pickaxe_cart = Card::create([
            "type" => "action",
            "name" => "sabotage_pickaxe_cart",
        ]);
        $action_sabotage_light_cart = Card::create([
            "type" => "action",
            "name" => "sabotage_light_cart"
        ]);

        // Recover cards
        $action_recover_pickaxe = Card::create([
            "type" => "action",
            "name" => "recover_pickaxe",
        ]);
        $action_recover_light = Card::create([
            "type" => "action",
            "name" => "recover_light",
        ]);
        $action_recover_cart = Card::create([
            "type" => "action",
            "name" => "recover_cart",
        ]);

        // Recover combo cards
        $action_recover_pickaxe_light = Card::create([
            "type" => "action",
            "name" => "recover_pickaxe_light",
        ]);
        $action_recover_pickaxe_cart = Card::create([
            "type" => "action",
            "name" => "recover_pickaxe_cart",
        ]);
        $action_recover_light_cart = Card::create([
            "type" => "action",
            "name" => "recover_light_cart",
        ]);

        // Demolish cards
        $action_demolish = Card::create([
            "type" => "action",
            "name" => "demolish",
        ]);

        // Enlighten cards
        $action_enlighten = Card::create([
            "type" => "action",
            "name" => "enlighten",
        ]);

        // ------------------------------------------------
        // Tunnel cards
        // ------------------------------------------------
        
        // 1 opening cards
        $tunnel_single_one = Card::create([
            "type" => "tunnel",
            "name" => "single_top",
            "open_positions" => ["top"],
        ]);
        $tunnel_single_two = Card::create([
            "type" => "tunnel",
            "name" => "single_right",
            "open_positions" => ["right"],
        ]);
        $tunnel_single_three = Card::create([
            "type" => "tunnel",
            "name" => "single_bottom",
            "open_positions" => ["bottom"],
        ]);
        $tunnel_single_four = Card::create([
            "type" => "tunnel",
            "name" => "single_left",
            "open_positions" => ["left"],
        ]);

        // 2 opening cards
        $tunnel_double_one = Card::create([
            "type" => "tunnel",
            "name" => "double_top_right",
            "open_positions" => ["top", "right"],
        ]);
        $tunnel_double_two = Card::create([
            "type" => "tunnel",
            "name" => "double_right_bottom",
            "open_positions" => ["right", "bottom"],
        ]);
        $tunnel_double_three = Card::create([
            "type" => "tunnel",
            "name" => "double_bottom_left",
            "open_positions" => ["bottom", "left"],
        ]);
        $tunnel_double_four = Card::create([
            "type" => "tunnel",
            "name" => "double_left_top",
            "open_positions" => ["left", "top"],
        ]);
        $tunnel_double_five = Card::create([
            "type" => "tunnel",
            "name" => "double_top_bottom",
            "open_positions" => ["top", "bottom"],
        ]);

        // 3 opening cards
        $tunnel_triple_one = Card::create([
            "type" => "tunnel",
            "name" => "triple_top_right_bottom",
            "open_positions" => ["top", "right", "bottom"],
        ]);
        $tunnel_triple_two = Card::create([
            "type" => "tunnel",
            "name" => "triple_right_bottom_left",
            "open_positions" => ["right", "bottom", "left"],
        ]);
        $tunnel_triple_three = Card::create([
            "type" => "tunnel",
            "name" => "triple_bottom_left_top",
            "open_positions" => ["bottom", "left", "top"],
        ]);
        $tunnel_triple_four = Card::create([
            "type" => "tunnel",
            "name" => "triple_left_top_right",
            "open_positions" => ["left", "top", "right"],
        ]);

        // 4 opening cards
        $tunnel_quadruple_one = Card::create([
            "type" => "tunnel",
            "name" => "quadruple",
            "open_positions" => ["top", "right", "bottom", "left"],
        ]);
    }
}
