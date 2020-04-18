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
            "open_positions" => ["top", "right", "bottom", "left"],
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
            "open_positions" => ["top", "right", "bottom", "left"],
        ]);

        $gold_location_gold_card = Card::create([
            "type" => "gold",
            "text" => "Gold",
            "name" => "gold",
            "description" => "This gold location contained gold! Awesome.",
            "open_positions" => ["top", "right", "bottom", "left"],
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
            "name" => "thiefery",
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
        
        // 1 opening cards
        $tunnel_single_one = Card::create([
            "type" => "tunnel",
            "name" => "single_top",
            "open_positions" => ["top", "center"],
        ]);
        $tunnel_single_two = Card::create([
            "type" => "tunnel",
            "name" => "single_right",
            "open_positions" => ["right", "center"],
        ]);
        $tunnel_single_three = Card::create([
            "type" => "tunnel",
            "name" => "single_bottom",
            "open_positions" => ["bottom", "center"],
        ]);
        $tunnel_single_four = Card::create([
            "type" => "tunnel",
            "name" => "single_left",
            "open_positions" => ["left", "center"],
        ]);

        // 2 opening cards
        $tunnel_double_one = Card::create([
            "type" => "tunnel",
            "name" => "double_top_right",
            "open_positions" => ["top", "right", "center"],
        ]);
        $tunnel_double_two = Card::create([
            "type" => "tunnel",
            "name" => "double_right_bottom",
            "open_positions" => ["right", "bottom", "center"],
        ]);
        $tunnel_double_three = Card::create([
            "type" => "tunnel",
            "name" => "double_bottom_left",
            "open_positions" => ["bottom", "left", "center"],
        ]);
        $tunnel_double_four = Card::create([
            "type" => "tunnel",
            "name" => "double_left_top",
            "open_positions" => ["left", "top", "center"],
        ]);
        $tunnel_double_five = Card::create([
            "type" => "tunnel",
            "name" => "double_top_bottom",
            "open_positions" => ["top", "bottom", "center"],
        ]);
        $tunnel_double_six = Card::create([
            "type" => "tunnel",
            "name" => "double_left_right",
            "open_positions" => ["left", "right", "center"],
        ]);

        // 3 opening cards
        $tunnel_triple_one = Card::create([
            "type" => "tunnel",
            "name" => "triple_top_right_bottom",
            "open_positions" => ["top", "right", "bottom", "center"],
        ]);
        $tunnel_triple_two = Card::create([
            "type" => "tunnel",
            "name" => "triple_right_bottom_left",
            "open_positions" => ["right", "bottom", "left", "center"],
        ]);
        $tunnel_triple_three = Card::create([
            "type" => "tunnel",
            "name" => "triple_bottom_left_top",
            "open_positions" => ["bottom", "left", "top", "center"],
        ]);
        $tunnel_triple_four = Card::create([
            "type" => "tunnel",
            "name" => "triple_left_top_right",
            "open_positions" => ["left", "top", "right", "center"],
        ]);

        // 4 opening cards
        $tunnel_quadruple_one = Card::create([
            "type" => "tunnel",
            "name" => "quadruple",
            "open_positions" => ["top", "right", "bottom", "left", "center"],
        ]);
    }
}
