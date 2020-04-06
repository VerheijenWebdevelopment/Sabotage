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
        // General cards
        // ------------------------------------------------

        $start_card = Card::create([
            "type" => "start",
            "text" => "Start",
            "name" => "start",
            "description" => "This is where the journey begins.",
        ]);

        $gold_location_card = Card::create([
            "type" => "gold_location",
            "text" => "Gold Location",
            "name" => "gold_location",
            "description" => "Possible location of shiny gold."
        ]);

        // ------------------------------------------------
        // Action cards
        // ------------------------------------------------

        // Sabotage cards
        $action_sabotage_pickaxe = Card::create([
            "type" => "action",
            "text" => "Sabotage Pickaxe",
            "name" => "sabotage_pickaxe",
            "description" => "Disable another player's pickaxe.",
        ]);
        $action_sabotage_light = Card::create([
            "type" => "action",
            "text" => "Sabotage Light",
            "name" => "sabotage_light",
            "description" => "Disable another player's lantern."
        ]);
        $action_sabotage_cart = Card::create([
            "type" => "action",
            "text" => "Sabotage Cart",
            "name" => "sabotage_cart",
            "description" => "Disable another player's cart."
        ]);

        // Sabotage combo cards
        $action_sabotage_pickaxe_light = Card::create([
            "type" => "action",
            "text" => "Sabotage Pickaxe & Light",
            "name" => "sabotage_pickaxe_light",
            "description" => "Disable another player's pickaxe or lantern.",
        ]);
        $action_sabotage_pickaxe_cart = Card::create([
            "type" => "action",
            "text" => "Sabotage Pickaxe & Cart",
            "name" => "sabotage_pickaxe_cart",
            "description" => "Disable another player's pickaxe or cart.",
        ]);
        $action_sabotage_light_cart = Card::create([
            "type" => "action",
            "text" => "Sabotage Light & Cart",
            "name" => "sabotage_light_cart",
            "description" => "Disable another player's lantern or cart.",
        ]);

        // Recover cards
        $action_recover_pickaxe = Card::create([
            "type" => "action",
            "text" => "Recover Pickaxe",
            "name" => "recover_pickaxe",
            "description" => "Recover a player's disabled pickaxe.",
        ]);
        $action_recover_light = Card::create([
            "type" => "action",
            "text" => "Recover Light",
            "name" => "recover_light",
            "description" => "Recover a player's disabled lantern.",
        ]);
        $action_recover_cart = Card::create([
            "type" => "action",
            "text" => "Recover Cart",
            "name" => "recover_cart",
            "description" => "Recover a player's disabled cart.",
        ]);

        // Recover combo cards
        $action_recover_pickaxe_light = Card::create([
            "type" => "action",
            "text" => "Recover Pickaxe & Light",
            "name" => "recover_pickaxe_light",
            "description" => "Recover a player's disabled pickaxe or lantern.",
        ]);
        $action_recover_pickaxe_cart = Card::create([
            "type" => "action",
            "text" => "Recover Pickaxe & Cart",
            "name" => "recover_pickaxe_cart",
            "description" => "Recover a player's disabled pickaxe or cart.",
        ]);
        $action_recover_light_cart = Card::create([
            "type" => "action",
            "text" => "Recover Light & Cart",
            "name" => "recover_light_cart",
            "description" => "Recover a player's disabled lantern or cart.",
        ]);

        // Demolish cards
        $action_demolish = Card::create([
            "type" => "action",
            "text" => "Collapse Tunnel",
            "name" => "collapse",
            "description" => "Destroy a placed tunnel card.",
        ]);

        // Enlighten cards
        $action_enlighten = Card::create([
            "type" => "action",
            "text" => "Enlighten",
            "name" => "enlighten",
            "description" => "Check one of the gold locations and be enlightened with it's contents.",
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
        $tunnel_double_six = Card::create([
            "type" => "tunnel",
            "name" => "double_left_right",
            "open_positions" => ["left", "right"],
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
