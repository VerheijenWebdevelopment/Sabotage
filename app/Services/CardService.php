<?php

namespace App\Services;

use Image;

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

        // // Add 2 random combo sabotage cards
        // $actionSabotageCombos = [
        //     $this->findByName("sabotage_pickaxe_light"),
        //     $this->findByName("sabotage_pickaxe_cart"),
        //     $this->findByName("sabotage_light_cart"),
        // ];
        // for ($i < 0; $i < 2; $i++) {
        //     $out[] = $actionSabotageCombos[rand(0, 2)]->id;
        // }

        // Add 2 of each single recover card
        $actionRecoverCart = $this->findByName("recover_cart");
        $actionRecoverLight = $this->findByName("recover_light");
        $actionRecoverPickaxe = $this->findByName("recover_pickaxe");
        for ($i = 0; $i < 3; $i++) {
            $out[] = $actionRecoverCart->id;
            $out[] = $actionRecoverLight->id;
            $out[] = $actionRecoverPickaxe->id;
        }
        
        // // Add 1 of each combo recover card
        $actionRecoverCombos = [
            $this->findByName("recover_pickaxe_light"),
            $this->findByName("recover_pickaxe_cart"),
            $this->findByName("recover_light_cart"),
        ];
        for ($i = 0; $i < 1; $i++) {
            $out[] = $actionRecoverCombos[rand(0, 2)]->id;
        }

        // Add 3 demolish cards
        $actionDemolish = $this->findByName("collapse");
        for ($i = 0; $i < 3; $i++) $out[] = $actionDemolish->id;

        // Add 6 enlighten cards
        $actionEnlighten = $this->findByName("enlighten");
        for ($i = 0; $i < 6; $i++) $out[] = $actionEnlighten->id;

        //
        // Tunnel cards
        //

        $out[] = 27;
        $out[] = 27;
        $out[] = 27;
        $out[] = 27;
        $out[] = 27;

        $out[] = 28;
        $out[] = 28;
        $out[] = 28;
        $out[] = 28;

        $out[] = 31;
        $out[] = 31;
        $out[] = 31;
        $out[] = 31;

        $out[] = 32;
        $out[] = 32;
        $out[] = 32;

        $out[] = 33;
        $out[] = 33;
        $out[] = 33;
        $out[] = 33;
        $out[] = 33;

        $out[] = 34;
        $out[] = 34;
        $out[] = 34;
        $out[] = 34;
        $out[] = 34;

        $out[] = 37;
        $out[] = 37;
        $out[] = 37;
        $out[] = 37;
        $out[] = 37;


        // Add 1 of every one slot card (x4)
        $out[] = $this->findByName("single_top")->id;
        $out[] = $this->findByName("single_right")->id;
        $out[] = $this->findByName("single_bottom")->id;
        // $out[] = $this->findByName("single_left")->id;
        
        // // Add 5 of all the corner 2 slot cards (x20)
        // $twoSlotTunnelOne = $this->findByName("double_top_right");
        // $twoSlotTunnelTwo = $this->findByName("double_right_bottom");
        // $twoSlotTunnelThree = $this->findByName("double_bottom_left");
        // $twoSlotTunnelFour = $this->findByName("double_left_top");
        // for ($i = 0; $i < 5; $i++) {
        //     $out[] = $twoSlotTunnelOne->id;
        //     $out[] = $twoSlotTunnelTwo->id;
        //     $out[] = $twoSlotTunnelThree->id;
        //     $out[] = $twoSlotTunnelFour->id;
        // }

        // // Add 4 of all of the straight 2 slot cards (x8)
        // $twoSlotTunnelFive = $this->findByName("double_top_bottom");
        // $twoSlotTunnelSix = $this->findByName("double_left_right");
        // for ($i = 0; $i < 2; $i++) {
        //     $out[] = $twoSlotTunnelFive->id;
        //     $out[] = $twoSlotTunnelSix->id;
        // }

        // // Add 2 of every three slot card (x8)
        // $threeSlotTunnelOne = $this->findByName("triple_top_right_bottom");
        // $threeSlotTunnelTwo = $this->findByName("triple_right_bottom_left");
        // $threeSlotTunnelThree = $this->findByName("triple_bottom_left_top");
        // $threeSlotTunnelFour = $this->findByName("triple_left_top_right");
        // for ($i = 0; $i < 1; $i++) {
        //     $out[] = $threeSlotTunnelOne->id;
        //     $out[] = $threeSlotTunnelTwo->id;
        //     $out[] = $threeSlotTunnelThree->id;
        //     $out[] = $threeSlotTunnelFour->id;
        // }

        // // Add 4 of the four slot cards (x4)
        // $fourSlotTunnel = $this->findByName("quadruple");
        // for ($i = 0; $i < 4; $i++) $out[] = $fourSlotTunnel->id;

        // Shuffle the deck
        shuffle($out);

        // Return the generated list of card id's
        return $out;
    }

    public function generateCardImage($id)
    {
        // Image dimensions
        $w = 260;
        $h = 400;

        // Create a new canvas to paint our art on
        $image = Image::canvas($w, $h, "#333");

        // Find the card we want to generate the image of
        $card = $this->find($id);

        // If we failed to find a card
        if (!$card)
        {
            $image->text("Unknown card", ($w/2), ($h/2), function($font) {
                $font->size(24);
                $font->align("center");
                $font->valign("center");
                $font->color("#ffffff");
                $font->file(public_path("storage/fonts/SpecialElite.ttf"));
            });
        }
        // If we managed to find the card
        else
        {
            // If we're dealing with a start card
            if ($card->type == "start")
            {
                $image = $this->addTunnel($image, $card);
                $image->text("Start", ($w/2), ($h/2), function($font) {
                    $font->size(24);
                    $font->align("center");
                    $font->valign("center");
                    $font->color("#ffffff");
                    $font->file(public_path("storage/fonts/SpecialElite.ttf"));
                });
            }
            // If we're dealing with a gold location card
            elseif ($card->type == "gold_location")
            {
                $image->text("Gold Location", ($w/2), ($h/2), function($font) {
                    $font->size(24);
                    $font->align("center");
                    $font->valign("center");
                    $font->color("#ffffff");
                    $font->file(public_path("storage/fonts/SpecialElite.ttf"));
                });
            }
            // Gold location > Coal card
            elseif ($card->type == "coal")
            {
                $image = $this->addTunnel($image, $card);
                $image->text("Coal", ($w/2), ($h/2), function($font) {
                    $font->size(24);
                    $font->align("center");
                    $font->valign("center");
                    $font->color("#ffffff");
                    $font->file(public_path("storage/fonts/SpecialElite.ttf"));
                });
            }
            // Gold location > Gold card
            elseif ($card->type == "gold")
            {
                $image = $this->addTunnel($image, $card);
                $image->text("Gold", ($w/2), ($h/2), function($font) {
                    $font->size(24);
                    $font->align("center");
                    $font->valign("center");
                    $font->color("#ffd900");
                    $font->file(public_path("storage/fonts/SpecialElite.ttf"));
                });
            }
            // If we're dealing with an action card
            elseif ($card->type == "action")
            {
                $image = $this->addHeaderText($image, "Action Card");
                $image = $this->addCardLabelText($image, $card->text);
            }
            // If we're dealing with a tunnel card
            elseif ($card->type == "tunnel")
            {
                $image = $this->addTunnel($image, $card);
                // $image = $this->addHeaderText($image, "Tunnel Card");
            }
        }

        return $image;
    }

    private function addHeaderText(\Intervention\Image\Image $image, $text)
    {
        // Header dimensions
        $w = 260;
        $h = 40;

        // Draw header background
        $image->rectangle(0, 0, $w, $h, function($draw) {
            $draw->background("rgba(0, 0, 0, 0.25)");
        });

        // Write header text
        $image->text($text, ($w/2), ($h/2), function($font) {
            $font->size(16);
            $font->align("center");
            $font->valign("center");
            $font->color("#ffffff");
            $font->file(public_path("storage/fonts/SpecialElite.ttf"));
        });

        // Return updated image
        return $image;
    }

    private function addCardLabelText(\Intervention\Image\Image $image, $text)
    {
        // Write card label
        $image->text($text, 130, 350, function($font) use ($text) {
            if (strlen($text) >= 20) {
                $font->size(16);
            } else {
                $font->size(18);
            }
            $font->align("center");
            $font->valign("center");
            $font->color("#ffffff");
            $font->file(public_path("storage/fonts/SpecialElite.ttf"));
        });

        // Return updated image
        return $image;
    }

    private function addTunnel(\Intervention\Image\Image $image, Card $card, $color = "#7a4600")
    {
        // Image dimensions
        $w = 260;
        $h = 400;

        // Grid dimensions
        $gw  = $w / 3;
        $ghs = $gw;
        $ghl = ($h - $gw) / 2;

        // Draw the center tile for all tunnel types
        $image->rectangle($gw, $ghl, ($gw*2), ($ghs + $ghl), function($draw) use ($color) {
            $draw->background($color);
        });

        // Draw all the open position tiles
        foreach ($card->open_positions as $position)
        {
            switch ($position)
            {
                case "top":
                    $image->rectangle($gw, 0, ($gw*2), $ghl, function($draw) use ($color) {
                        $draw->background($color);
                    });
                break;
                case "right":
                    $image->rectangle(($gw*2), $ghl, ($gw*3), ($ghs+$ghl), function($draw) use ($color) {
                        $draw->background($color);
                    });
                break;
                case "bottom":
                    $image->rectangle($gw, ($ghs+$ghl), ($gw*2), $h, function($draw) use ($color) {
                        $draw->background($color);
                    });
                break;
                case "left":
                    $image->rectangle(0, $ghl, $gw, ($ghs+$ghl), function($draw) use ($color) {
                        $draw->background($color);
                    });
                break;
            }
        }

        // Return the updated image
        return $image;
    }

    public function generateRewardCards(Game $game)
    {
        // Determine how many cards we should return
        $numCards = $game->players->count();

        // Generate the pool of cards to grab the cards from
        $pool = $this->generateRewardCardPool();

        // Return the top number of cards from the pool
        return array_slice($pool, 0, $numCards);
    }

    private function generateRewardCardPool()
    {
        // List of reward card id's that are available
        $output = [];

        // Grab all of the gold reward cards
        $one_gold = $this->findByName("reward_one_gold");
        $two_gold = $this->findByName("reward_two_gold");
        $three_gold = $this->findByName("reward_three_gold");
        $four_gold = $this->findByName("reward_four_gold");

        // Add the cards to the pool
        for ($i = 0; $i < 20; $i++) $output[] = $one_gold->id;
        for ($i = 0; $i < 10; $i++) $output[] = $two_gold->id;
        for ($i = 0; $i < 5; $i++) $output[] = $three_gold->id;
        for ($i = 0; $i < 2; $i++) $output[] = $four_gold->id;

        // Shuffle that fucker
        shuffle($output);

        // And return it
        return $output;
    }
}
