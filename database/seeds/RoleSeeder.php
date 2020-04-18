<?php

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("roles")->delete();

        $saboteur = Role::create([
            "name" => "saboteur",
            "label" => "Saboteur",
            "description" => "De saboteurs winnen wanneer het goud niet wordt bereikt. Hun doel is dan ook om de goudzoekers te saboteren, zoals de naam al doet vermoeden.",
        ]);

        $digger_blue = Role::create([
            "name" => "blue_digger",
            "label" => "Goudzoeker",
            "description" => "Goudzoeker uit het blauwe team. Jouw doel is om het goud te bereiken dat op 1 van de 3 mogelijke goudlocaties ligt te wachten op je.",
            "color" => "blue",
        ]);

        $digger_green = Role::create([
            "name" => "green_digger",
            "label" => "Goudzoeker",
            "description" => "Goudzoeker uit het groene team. Jouw doel is om het goud te bereiken dat op 1 van de 3 mogelijke goudlocaties ligt te wachten op je.",
            "color" => "green"
        ]);

        $chef = Role::create([
            "name" => "chef",
            "label" => "Chef",
            "description" => "Als chef ben jij de leider van de beide goudzoeker teams. Jij wint dus ook als 1 van de goedzoeker teams het goud vinden. Je krijgt echter wel 1 goudstuk minder dan het winnende team.",
        ]);
        
        $profiteer = Role::create([
            "name" => "profiteer",
            "label" => "Profiteur",
            "description" => "De profiteur wint in alle gevallen, maar krijgt 2 goudstukken minder dan de andere winnaars. Als jij als profiteur alleen wint krijgt je 3 goudstukken.",
        ]);

        $geologist = Role::create([
            "name" => "geologist",
            "label" => "Geoloog",
            "description" => "De geologen graven uitsluitend voor eigen belang. Jij bent niet geintresseerd in het goud maar bent op zoek naar kristallen die door de tunnel verspreid zijn. Je krijgt 1 goudstuk per kristal dat bereikbaar is aan het einde van de ronde.",
        ]);
    }
}
