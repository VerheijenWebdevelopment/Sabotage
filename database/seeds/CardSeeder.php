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
            "open_positions" => ["top", "right", "bottom", "left", "center"],
        ]);

        $gold_location_card = Card::create([
            "type" => "gold_location",
            "text" => "Gold Location",
            "name" => "gold_location",
            "description" => "Possible location of shiny gold."
        ]);

        $gold_location_coal_card = Card::create([
            "type" => "coal",
            "text" => "Coal",
            "name" => "coal",
            "description" => "This gold location contained coal. Bummer.",
            "open_positions" => ["top", "right", "bottom", "left", "center"],
        ]);

        $gold_location_gold_card = Card::create([
            "type" => "gold",
            "text" => "Gold",
            "name" => "gold",
            "description" => "This gold location contained gold! Awesome.",
            "open_positions" => ["top", "right", "bottom", "left", "center"],
        ]);

        // ------------------------------------------------
        // Action cards
        // ------------------------------------------------
            
        // Sabotage cards
        $action_sabotage_pickaxe = Card::create([
            "type" => "action",
            "text" => "Saboteer Pickaxe",
            "name" => "sabotage_pickaxe",
            "description" => "Schakel de pickaxe uit van een speler naar keuze.",
        ]);
        $action_sabotage_light = Card::create([
            "type" => "action",
            "text" => "Saboteer Light",
            "name" => "sabotage_light",
            "description" => "Schakel de lantaarn uit van een speler naar keuze."
        ]);
        $action_sabotage_cart = Card::create([
            "type" => "action",
            "text" => "Saboteer Cart",
            "name" => "sabotage_cart",
            "description" => "Schakel de minecart uit van een speler naar keuze."
        ]);

        // Sabotage combo cards
        $action_sabotage_pickaxe_light = Card::create([
            "type" => "action",
            "text" => "Saboteer Pickaxe / Light",
            "name" => "sabotage_pickaxe_light",
            "description" => "Schakel de pickaxe of lantaarn uit van een speler naar keuze.",
        ]);
        $action_sabotage_pickaxe_cart = Card::create([
            "type" => "action",
            "text" => "Saboteer Pickaxe / Cart",
            "name" => "sabotage_pickaxe_cart",
            "description" => "Schakel de pickaxe of minecart uit van een speler naar keuze.",
        ]);
        $action_sabotage_light_cart = Card::create([
            "type" => "action",
            "text" => "Saboteer Light / Cart",
            "name" => "sabotage_light_cart",
            "description" => "Schakel de lantaarn of minecart uit van een speler naar keuze.",
        ]);

        // Recover cards
        $action_recover_pickaxe = Card::create([
            "type" => "action",
            "text" => "Herstel Pickaxe",
            "name" => "recover_pickaxe",
            "description" => "Herstel een uitgeschakelde pickaxe van een speler naar keuze.",
        ]);
        $action_recover_light = Card::create([
            "type" => "action",
            "text" => "Herstel Light",
            "name" => "recover_light",
            "description" => "Herstel een uitgeschakelde lantaarn van een speler naar keuze.",
        ]);
        $action_recover_cart = Card::create([
            "type" => "action",
            "text" => "Herstel Cart",
            "name" => "recover_cart",
            "description" => "Herstel een uitgeschakelde minecart van een speler naar keuze.",
        ]);

        // Recover combo cards
        $action_recover_pickaxe_light = Card::create([
            "type" => "action",
            "text" => "Herstel Pickaxe / Light",
            "name" => "recover_pickaxe_light",
            "description" => "Herstel een uitgeschakelde pickaxe of lantaarn van een speler naar keuze.",
        ]);
        $action_recover_pickaxe_cart = Card::create([
            "type" => "action",
            "text" => "Herstel Pickaxe / Cart",
            "name" => "recover_pickaxe_cart",
            "description" => "Herstel een uitgeschakelde pickaxe of minecart van een speler naar keuze.",
        ]);
        $action_recover_light_cart = Card::create([
            "type" => "action",
            "text" => "Herstel Light / Cart",
            "name" => "recover_light_cart",
            "description" => "Herstel een uitgeschakelde lantaarn of minecart van een speler naar keuze.",
        ]);

        // Demolish cards
        $action_demolish = Card::create([
            "type" => "action",
            "text" => "Instortgevaar",
            "name" => "collapse",
            "description" => "Wanneer je deze kaart speelt mag je 1 van de gespeelde tunnelkaarten verwijderen uit het spel.",
        ]);

        // Enlighten cards
        $action_enlighten = Card::create([
            "type" => "action",
            "text" => "Plan",
            "name" => "enlighten",
            "description" => "Wanneer je deze kaart speelt mag je 1 van de goudlocaties selecteren om te kijken of er goud ligt.",
        ]);
        
        // Thief cards
        $action_thief = Card::create([
            "type" => "action",
            "text" => "Diefstal",
            "name" => "thief",
            "description" => "Deze kaart blijft actief zolang hij 'op tafel' ligt. Aan het einde van de ronde, na het verdelen van de goudstukken, mag je van 1 speler naar keuze 1 goudstuk stelen (indien mogelijk).",
        ]);

        // Dont touch cards
        $action_dont_touch = Card::create([
            "type" => "action",
            "text" => "Afblijven",
            "name" => "dont_touch",
            "description" => "Bij het spelen van deze kaart mag je 1 gespeelde diefstalkaart van tafel verwijderen.",
        ]);

        // Imprison card
        $action_imprison = Card::create([
            "type" => "action",
            "text" => "Gevangen!",
            "name" => "imprison",
            "description" => "Bij het spelen van deze kaart mag je 1 speler kiezen die vervolgens gevangen genomen wordt. Deze speler mag geen tunnelkaarten meer spelen en krijgt geen deel van de schat aan het einde van de ronde. Ook een diefstal kaart mag niet worden gebruikt mocht die in het spel zijn.",
        ]);

        // Freedom card
        $action_free = Card::create([
            "type" => "action",
            "text" => "Eindelijk vrij!",
            "name" => "free",
            "description" => "Bij het spelen van deze kaart mag je iemand uit de gevangenis bevrijden.",
        ]);

        // Inspection card
        $action_inspection = Card::create([
            "type" => "action",
            "text" => "Inspectie",
            "name" => "inspection",
            "description" => "Bij het spelen van deze kaart mag je de dwergenkaart (rol) van 1 speler naar keuze bekijken.",
        ]);

        // Exchange hats card
        $action_exchange_hats = Card::create([
            "type" => "action",
            "text" => "Mutsen ruilen",
            "name" => "exchange_hats",
            "description" => "Bij het spelen van deze kaart mag je 1 speler kiezen (inclusief jezelf). De gekozen speler legt zijn dwergenkaart (rol) af en trekt daarna een nieuwe kaart van de overgebleven dwergenkaarten.",
        ]);

        // Exchange hands card
        $action_exchange_hands = Card::create([
            "type" => "action",
            "text" => "Handkaarten ruilen",
            "name" => "exchange_hands",
            "description" => "Bij het spelen van deze kaart kies je 1 andere speler uit. Jullie ruilen vervolgens al jullie hand kaarten met elkaar om, ook wanneer er ongelijke aantallen zijn. Ter compensatie mag de gekozen speler 1 nieuwe kaart trekken.",
        ]);

        // ------------------------------------------------
        // Tunnel cards
        // ------------------------------------------------
        

        // Single tunnel - no center
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_top",
            "open_positions" => ["top"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_right",
            "open_positions" => ["right"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_bottom",
            "open_positions" => ["bottom"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_left",
            "open_positions" => ["left"],
        ]);

        // Single tunnel - with center
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_center_top",
            "open_positions" => ["center", "top"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_center_right",
            "open_positions" => ["center", "right"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_center_bottom",
            "open_positions" => ["center", "bottom"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_center_left",
            "open_positions" => ["center", "left"],
        ]);

        // Double tunnel - with center
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_center_top_right",
            "open_positions" => ["center", "top", "right"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_center_right_bottom",
            "open_positions" => ["center", "right", "bottom"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_center_bottom_left",
            "open_positions" => ["center", "bottom", "left"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_center_left_top",
            "open_positions" => ["center", "left", "top"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_center_top_bottom",
            "open_positions" => ["center", "top", "bottom"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_center_left_right",
            "open_positions" => ["center", "left", "right"],
        ]);
        
        // Double tunnel - no center
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_top_right",
            "open_positions" => ["top", "right", "center"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_right_bottom",
            "open_positions" => ["right", "bottom", "center"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_bottom_left",
            "open_positions" => ["bottom", "left", "center"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_left_top",
            "open_positions" => ["left", "top", "center"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_top_bottom",
            "open_positions" => ["top", "bottom", "center"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_left_right",
            "open_positions" => ["left", "right", "center"],
        ]);

        // Triple tunnel - no center
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_top_right_bottom",
            "open_positions" => ["top", "right", "bottom"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_right_bottom_left",
            "open_positions" => ["right", "bottom", "left"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_bottom_left_top",
            "open_positions" => ["bottom", "left", "top"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_left_top_right",
            "open_positions" => ["left", "top", "right"],
        ]);

        // Triple tunnel - with center
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_center_top_right_bottom",
            "open_positions" => ["top", "right", "bottom", "center"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_center_right_bottom_left",
            "open_positions" => ["right", "bottom", "left", "center"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_center_bottom_left_top",
            "open_positions" => ["bottom", "left", "top", "center"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_center_left_top_right",
            "open_positions" => ["left", "top", "right", "center"],
        ]);
        
        // Quadruple cards
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_top_right_bottom_left",
            "open_positions" => ["top", "right", "bottom", "left"],
        ]);
        Card::create([
            "type" => "tunnel",
            "name" => "tunnel_center_top_right_bottom_left",
            "open_positions" => ["top", "right", "bottom", "left", "center"],
        ]);
    }
}
