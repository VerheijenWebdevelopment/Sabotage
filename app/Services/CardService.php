<?php

namespace App\Services;

use App\Models\Card;
use App\Models\Game;

use App\Traits\ModelServiceGetters;
use App\Contracts\ModelServiceContract;

class CardService implements ModelServiceContract
{
    use ModelServiceGetters;

    private $model;
    private $records;
    private $preloadedRecords;

    public function __construct()
    {
        $this->model =  "App\Models\Card";
    }

    public function preload($instance)
    {
        $instance->image_url = route("api.cards.generate-image", $instance->id);

        return $instance;
    }

    public function findByName($name)
    {
        foreach ($this->getAll() as $card)
        {
            if ($card->name == $name)
            {
                return $card;
            }
        }

        return false;
    }

    public function generateDeck(Game $game)
    {
        $out = [];

        //
        // Action cards
        //

        // Add 3 of each single sabotage card
        $actionSabotageCart = $this->findByName("sabotage_cart");
        $actionSabotageLight = $this->findByName("sabotage_light");
        $actionSabotagePickaxe = $this->findByName("sabotage_pickaxe");
        for ($i = 0; $i < 3; $i++) {
            $out[] = $actionSabotageCart->id;
            $out[] = $actionSabotageLight->id;
            $out[] = $actionSabotagePickaxe->id;
        }

        // Add 2 random combo sabotage cards
        $actionSabotageCombos = [
            $this->findByName("sabotage_pickaxe_light"),
            $this->findByName("sabotage_pickaxe_cart"),
            $this->findByName("sabotage_light_cart"),
        ];
        for ($i < 0; $i < 2; $i++) {
            $out[] = $actionSabotageCombos[rand(0, 2)]->id;
        }

        // Add 3 of each single recover card
        $actionRecoverCart = $this->findByName("recover_cart");
        $actionRecoverLight = $this->findByName("recover_light");
        $actionRecoverPickaxe = $this->findByName("recover_pickaxe");
        for ($i = 0; $i < 3; $i++) {
            $out[] = $actionRecoverCart->id;
            $out[] = $actionRecoverLight->id;
            $out[] = $actionRecoverPickaxe->id;
        }
        
        // Add 2 random combo recover cards
        $actionRecoverCombos = [
            $this->findByName("recover_pickaxe_light"),
            $this->findByName("recover_pickaxe_cart"),
            $this->findByName("recover_light_cart"),
        ];
        for ($i = 0; $i < 2; $i++) {
            $out[] = $actionRecoverCombos[rand(0, 2)]->id;
        }

        // Add 4 demolish cards
        $actionDemolish = $this->findByName("collapse");
        for ($i = 0; $i < 4; $i++) $out[] = $actionDemolish->id;

        // Add 4 enlighten cards
        $actionEnlighten = $this->findByName("enlighten");
        for ($i = 0; $i < 4; $i++) $out[] = $actionEnlighten->id;

        //
        // Tunnel cards
        //

        // Add 1 of every one slot card (x4)
        $out[] = [
            $this->findByName("single_top")->id,
            $this->findByName("single_right")->id,
            $this->findByName("single_bottom")->id,
            $this->findByName("single_left")->id,
        ];
        
        // Add 5 of all the corner 2 slot cards (x20)
        $twoSlotTunnelOne = $this->findByName("double_top_right");
        $twoSlotTunnelTwo = $this->findByName("double_right_bottom");
        $twoSlotTunnelThree = $this->findByName("double_bottom_left");
        $twoSlotTunnelFour = $this->findByName("double_left_top");
        for ($i = 0; $i < 5; $i++) {
            $out[] = $twoSlotTunnelOne->id;
            $out[] = $twoSlotTunnelTwo->id;
            $out[] = $twoSlotTunnelThree->id;
            $out[] = $twoSlotTunnelFour->id;
        }

        // Add 4 of all of the straight 2 slot cards (x8)
        $twoSlotTunnelFive = $this->findByName("double_top_bottom");
        $twoSlotTunnelSix = $this->findByName("double_left_right");
        for ($i = 0; $i < 4; $i++) {
            $out[] = $twoSlotTunnelFive->id;
            $out[] = $twoSlotTunnelSix->id;
        }

        // Add 2 of every three slot card (x8)
        $threeSlotTunnelOne = $this->findByName("triple_top_right_bottom");
        $threeSlotTunnelTwo = $this->findByName("triple_right_bottom_left");
        $threeSlotTunnelThree = $this->findByName("triple_bottom_left_top");
        $threeSlotTunnelFour = $this->findByName("triple_left_top_right");
        for ($i = 0; $i < 2; $i++) {
            $out[] = $threeSlotTunnelOne->id;
            $out[] = $threeSlotTunnelTwo->id;
            $out[] = $threeSlotTunnelThree->id;
            $out[] = $threeSlotTunnelFour->id;
        }

        // Add 4 of the four slot cards (x4)
        $fourSlotTunnel = $this->findByName("quadruple");
        for ($i = 0; $i < 4; $i++) $out[] = $fourSlotTunnel->id;
        
        // Shuffle the deck
        shuffle($out);

        // Return the generated list of card id's
        return $out;
    }
}
