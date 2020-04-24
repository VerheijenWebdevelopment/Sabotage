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
        // Grab all action cards
        //

        $action_sabotage_cart = $this->findByName("sabotage_cart");
        $action_sabotage_light = $this->findByName("sabotage_light");
        $action_sabotage_pickaxe = $this->findByName("sabotage_pickaxe");

        $action_sabotage_pickaxe_light = $this->findByName("sabotage_pickaxe_light");
        $action_sabotage_pickaxe_cart = $this->findByName("sabotage_pickaxe_cart");
        $action_sabotage_light_cart = $this->findByName("sabotage_light_cart");

        $action_recover_cart = $this->findByName("recover_cart");
        $action_recover_light = $this->findByName("recover_light");
        $action_recover_pickaxe = $this->findByName("recover_pickaxe");

        $action_recover_pickaxe_light = $this->findByName("recover_pickaxe_light");
        $action_recover_pickaxe_cart = $this->findByName("recover_pickaxe_cart");
        $action_recover_light_cart = $this->findByName("recover_light_cart");

        $action_enlighten = $this->findByName("enlighten");
        $action_collapse = $this->findByName("collapse");
        $action_thief = $this->findByName("thief");
        $action_dont_touch = $this->findByName("dont_touch");
        $action_imprison = $this->findByName("imprison");
        $action_free = $this->findByName("free");
        $action_inspection = $this->findByName("inspection");
        $action_exchange_hats = $this->findByName("exchange_hats");
        $action_exchange_hands = $this->findByName("exchange_hands");

        //
        // Generate action cards 
        //

        // // Sabotage cards
        // for ($i = 0; $i < 3; $i++)
        // {
        //     $out[] = $action_sabotage_cart->id;
        //     $out[] = $action_sabotage_light->id;
        //     $out[] = $action_sabotage_pickaxe->id;
        // }

        // // Recover cards
        // for ($i = 0; $i < 3; $i++)
        // {
        //     $out[] = $action_recover_cart->id;
        //     $out[] = $action_recover_light->id;
        //     $out[] = $action_recover_pickaxe->id;
        // }
        
        // // Combo recover cards
        // $recoverCombos = [
        //     $action_recover_pickaxe_light,
        //     $action_recover_pickaxe_cart,
        //     $action_recover_light_cart,
        // ];
        // $out[] = $recoverCombos[rand(0, 2)]->id;

        // // Add 3 collapse cards
        // $out[] = $action_collapse->id;
        // $out[] = $action_collapse->id;
        // $out[] = $action_collapse->id;
        // $out[] = $action_collapse->id;
        // $out[] = $action_collapse->id;
        // $out[] = $action_collapse->id;
        // $out[] = $action_collapse->id;
        // $out[] = $action_collapse->id;

        // // Add 6 enlighten cards
        // $out[] = $action_enlighten->id;
        // $out[] = $action_enlighten->id;
        // $out[] = $action_enlighten->id;
        // $out[] = $action_enlighten->id;
        // $out[] = $action_enlighten->id;
        // $out[] = $action_enlighten->id;

        // // Add 4 thiefery cards
        $out[] = $action_thief->id;
        $out[] = $action_thief->id;
        $out[] = $action_thief->id;
        $out[] = $action_thief->id;

        // // Add 3 dont touch cards
        // $out[] = $action_dont_touch->id;
        // $out[] = $action_dont_touch->id;
        // $out[] = $action_dont_touch->id;

        // // Add 2 exchange hats cards
        // $out[] = $action_exchange_hats->id;
        // $out[] = $action_exchange_hats->id;
        // $out[] = $action_exchange_hats->id;
        // $out[] = $action_exchange_hats->id;
        // $out[] = $action_exchange_hats->id;

        // // Add 2 inspection cards
        // $out[] = $action_inspection->id;
        // $out[] = $action_inspection->id;

        // // Add 2 exchange hands cards
        // $out[] = $action_exchange_hands->id;
        // $out[] = $action_exchange_hands->id;
        // $out[] = $action_exchange_hands->id;
        // $out[] = $action_exchange_hands->id;
        // $out[] = $action_exchange_hands->id;

        // // Add 3 imprison cards
        // $out[] = $action_imprison->id;
        // $out[] = $action_imprison->id;
        // $out[] = $action_imprison->id;

        // // Add 4 free cards
        // $out[] = $action_free->id;
        // $out[] = $action_free->id;
        // $out[] = $action_free->id;
        // $out[] = $action_free->id;

        //
        // Grab all tunnel cards
        //

        $tunnel_single_one = $this->findByName("tunnel_top");
        $tunnel_single_two = $this->findByName("tunnel_right");
        $tunnel_single_three = $this->findByName("tunnel_bottom");
        $tunnel_single_four = $this->findByName("tunnel_left");

        $tunnel_single_center_one = $this->findByName("tunnel_center_top");
        $tunnel_single_center_two = $this->findByName("tunnel_center_right");
        $tunnel_single_center_three = $this->findByName("tunnel_center_bottom");
        $tunnel_single_center_four = $this->findByName("tunnel_center_left");

        $tunnel_double_one = $this->findByName("tunnel_top_right");
        $tunnel_double_two = $this->findByName("tunnel_right_bottom");
        $tunnel_double_three = $this->findByName("tunnel_bottom_left");
        $tunnel_double_four = $this->findByName("tunnel_left_top");
        $tunnel_double_five = $this->findByName("tunnel_top_bottom");
        $tunnel_double_six = $this->findByName("tunnel_left_right");

        $tunnel_double_center_one = $this->findByName("tunnel_center_top_right");
        $tunnel_double_center_two = $this->findByName("tunnel_center_right_bottom");
        $tunnel_double_center_three = $this->findByName("tunnel_center_bottom_left");
        $tunnel_double_center_four = $this->findByName("tunnel_center_left_top");
        $tunnel_double_center_five = $this->findByName("tunnel_center_top_bottom");
        $tunnel_double_center_six = $this->findByName("tunnel_center_left_right");

        $tunnel_triple_one = $this->findByName("tunnel_top_right_bottom");
        $tunnel_triple_two = $this->findByName("tunnel_right_bottom_left");
        $tunnel_triple_three = $this->findByName("tunnel_bottom_left_top");
        $tunnel_triple_four = $this->findByName("tunnel_left_top_right");

        $tunnel_triple_center_one = $this->findByName("tunnel_center_top_right_bottom");
        $tunnel_triple_center_two = $this->findByName("tunnel_center_right_bottom_left");
        $tunnel_triple_center_three = $this->findByName("tunnel_center_bottom_left_top");
        $tunnel_triple_center_four = $this->findByName("tunnel_center_left_top_right");

        $tunnel_quadruple = $this->findByName("tunnel_top_right_bottom_left");

        $tunnel_quadruple_center = $this->findByName("tunnel_center_top_right_bottom_left");

        //
        // Generate tunnel cards
        //
        
        // TODO: grab these numbers from the game's saved settings
        $num_ladders = 4;
        $num_crystals = 8;

        // Gather the tunnels seperately first so we can post-process them
        $tunnels = [];

        // // Dead ends with 1 tile
        // $singleNoCenters = [
        //     $tunnel_single_one,
        //     $tunnel_single_two,
        //     $tunnel_single_three,
        //     $tunnel_single_four,
        // ];
        // $tunnels[] = $singleNoCenters[rand(0, 3)];
        // $tunnels[] = $singleNoCenters[rand(0, 3)];

        // // Dead ends with 2 tiles
        // $doubleNoCenters = [
        //     $tunnel_single_center_one,
        //     $tunnel_single_center_two,
        //     $tunnel_single_center_three,
        //     $tunnel_single_center_four,
        //     $tunnel_double_one,
        //     $tunnel_double_two,
        //     $tunnel_double_three,
        //     $tunnel_double_four,
        //     $tunnel_double_five,
        //     $tunnel_double_six,
        // ];
        // $tunnels[] = $doubleNoCenters[rand(0, 5)];
        // $tunnels[] = $doubleNoCenters[rand(0, 5)];
        // $tunnels[] = $doubleNoCenters[rand(0, 5)];
        // $tunnels[] = $doubleNoCenters[rand(0, 5)];

        // // Dead ends with 3 tiles
        // $tripleNoCenters = [
        //     $tunnel_triple_one,
        //     $tunnel_triple_two,
        //     $tunnel_triple_three,
        //     $tunnel_triple_four,
        // ];
        // $tunnels[] = $tripleNoCenters[rand(0, 3)];
        // $tunnels[] = $tripleNoCenters[rand(0, 3)];
        // $tunnels[] = $tripleNoCenters[rand(0, 3)];
        // $tunnels[] = $tripleNoCenters[rand(0, 3)];

        // // Dead ends with 4 tiles
        // $tunnels[] = $tunnel_quadruple;

        // // Connected tunnel 2 tiles 
        // $tunnels[] = $tunnel_double_center_one;
        // $tunnels[] = $tunnel_double_center_two;
        // $tunnels[] = $tunnel_double_center_three;
        // $tunnels[] = $tunnel_double_center_four;
        // $tunnels[] = $tunnel_double_center_five;
        // $tunnels[] = $tunnel_double_center_six;

        // // Connected tunnel 3 tiles
        // $tunnels[] = $tunnel_triple_center_one;
        // $tunnels[] = $tunnel_triple_center_two;
        // $tunnels[] = $tunnel_triple_center_three;
        // $tunnels[] = $tunnel_triple_center_four;

        // Connected tunnel 4 tiles
        $tunnels[] = $tunnel_quadruple_center;
        $tunnels[] = $tunnel_quadruple_center;
        $tunnels[] = $tunnel_quadruple_center;
        $tunnels[] = $tunnel_quadruple_center;
        $tunnels[] = $tunnel_quadruple_center;
        $tunnels[] = $tunnel_quadruple_center;
        $tunnels[] = $tunnel_quadruple_center;
        $tunnels[] = $tunnel_quadruple_center;
        $tunnels[] = $tunnel_quadruple_center;
        $tunnels[] = $tunnel_quadruple_center;
        $tunnels[] = $tunnel_quadruple_center;
        $tunnels[] = $tunnel_quadruple_center;
        $tunnels[] = $tunnel_quadruple_center;
        $tunnels[] = $tunnel_quadruple_center;
        $tunnels[] = $tunnel_quadruple_center;
        $tunnels[] = $tunnel_quadruple_center;
        $tunnels[] = $tunnel_quadruple_center;
        $tunnels[] = $tunnel_quadruple_center;
        $tunnels[] = $tunnel_quadruple_center;

        // Add the ladders and crystals to the tunnel cards randomly
        shuffle($tunnels);
        $ladders_placed = 0;
        $crystals_placed = 0;
        // Process all of the tunnels available
        for ($i = 0; $i < count($tunnels); $i++)
        {
            // If we need more ladders
            if ($ladders_placed < $num_ladders)
            {
                // If this tunnel has a ladder version
                $ladder_version = $this->findByName($tunnels[$i]->name."_ladder");
                if ($ladder_version)
                {
                    // Add the ladder version to the output & up the counter
                    $out[] = $ladder_version->id;
                    $ladders_placed++;
                }
                // If we could not find the ladder version 
                else
                {
                    $out[] = $tunnels[$i]->id;
                }
            }
            // If we need more crystals
            else if ($crystals_placed < $num_crystals)
            {
                // If this tunnel has a crystal version
                $crystal_version = $this->findByName($tunnels[$i]->name."_crystal");
                if ($crystal_version)
                {
                    // Add crystal version to the output & up the counter
                    $out[] = $crystal_version->id;
                    $crystals_placed++;
                }
                // If we could not find the crystal version
                else
                {
                    $out[] = $tunnels[$i]->id;
                }
            }
            // If we're good on ladders & crystals
            else
            {
                $out[] = $tunnels[$i]->id;
            }
        }

        // Shuffle the output to make things random
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
                $image = $this->addActionIcon($image, $card);
            }
            // If we're dealing with a tunnel card
            elseif ($card->type == "tunnel")
            {
                $image = $this->addTunnel($image, $card);
                if ($card->has_ladder) $image = $this->addLadder($image, $card, $w, $h);
                if ($card->has_crystal) $image = $this->addCrystal($image, $card, $w, $h);
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

        // Determine if this card has a center tile
        $hasCenterTile = in_array("center", $card->open_positions);

        // Draw all the open position tiles
        foreach ($card->open_positions as $position)
        {
            switch ($position)
            {
                case "center":
                    $image->rectangle($gw, $ghl, ($gw*2), ($ghs + $ghl), function($draw) use ($color) {
                        $draw->background($color);
                    });
                break;
                case "top":
                    if ($hasCenterTile)
                    {
                        $image->rectangle($gw, 0, ($gw*2), $ghl, function($draw) use ($color) {
                            $draw->background($color);
                        });
                    }
                    else
                    {
                        $image->rectangle($gw, 0, ($gw*2), $gw, function($draw) use ($color) {
                            $draw->background($color);
                        });
                    }
                break;
                case "right":
                    $image->rectangle(($gw*2), $ghl, ($gw*3), ($ghs+$ghl), function($draw) use ($color) {
                        $draw->background($color);
                    });
                break;
                case "bottom":
                    if ($hasCenterTile)
                    {
                        $image->rectangle($gw, ($ghs+$ghl), ($gw*2), $h, function($draw) use ($color) {
                            $draw->background($color);
                        });
                    }
                    else
                    {
                        $image->rectangle($gw, ($ghs+$ghl+($ghl-$ghs)), ($gw*2), $h, function($draw) use ($color) {
                            $draw->background($color);
                        });
                    }
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

    private function addCrystal(\Intervention\Image\Image $image, Card $card, $w, $h)
    {
        switch ($card->crystal_location)
        {
            case "center":
                $image->insert(public_path("storage/images/cards/crystal.png"), "center");
            break;

            case "top":
                $image->insert(public_path("storage/images/cards/crystal.png"), "top", 0, 0);
            break;
            
            case "right":
                $image->insert(public_path("storage/images/cards/crystal.png"), "right", 0, 0);
            break;
            
            case "bottom":
                $image->insert(public_path("storage/images/cards/crystal.png"), "bottom", 0, 0);
            break;
            
            case "left":
                $image->insert(public_path("storage/images/cards/crystal.png"), "left", 0, 0);
            break;
        }

        return $image;
    }

    private function addLadder(\Intervention\Image\Image $image, Card $card, $w, $h)
    {
        switch ($card->ladder_location)
        {
            case "center":
                $image->insert(public_path("storage/images/cards/ladder.png"), "top", 0, 145);
            break;

            case "top":
                $image->insert(public_path("storage/images/cards/ladder.png"), "top", 0, 0);
            break;
            
            case "right":
                $image->insert(public_path("storage/images/cards/ladder.png"), "right", 0, 0);
            break;
            
            case "bottom":
                $image->insert(public_path("storage/images/cards/ladder.png"), "bottom", 0, 0);
            break;
            
            case "left":
                $image->insert(public_path("storage/images/cards/ladder.png"), "left", 0, 0);
            break;
        }

        return $image;
    }

    private function addActionIcon(\Intervention\Image\Image $image, Card $card)
    {
        switch ($card->name)
        {
            case "exchange_hands":
                $image->insert(public_path("storage/images/cards/exchange_hands.png"), "center");
            break;
            case "exchange_hats":
                $image->insert(public_path("storage/images/cards/exchange_hats.png"), "center");
            break;
            case "inspection":
                $image->insert(public_path("storage/images/cards/inspection.png"), "center");
            break;
            case "free":
                $image->insert(public_path("storage/images/cards/escape.png"), "center");
            break;
            case "imprison":
                $image->insert(public_path("storage/images/cards/imprison.png"), "center");
            break;
            case "dont_touch":
                $image->insert(public_path("storage/images/cards/dont_touch.png"), "center");
            break;
            case "thief":
                $image->insert(public_path("storage/images/cards/thief.png"), "center");
            break;
            case "enlighten":
                $image->insert(public_path("storage/images/cards/enlighten.png"), "center");
            break;
            case "collapse":
                $image->insert(public_path("storage/images/cards/collapse.png"), "center");
            break;
            case "recover_pickaxe":

                $pickaxe = Image::canvas(150, 150);
                $pickaxe->insert(public_path("storage/images/cards/pickaxe.png"));
                $pickaxe->resize(100, 100);

                $recover = Image::canvas(150, 150);
                $recover->insert(public_path("storage/images/cards/recover.png"));
                $recover->resize(160, 160);

                $image->insert($pickaxe, "center");
                $image->insert($recover, "center");

            break;
            case "recover_cart":
                
                $cart = Image::canvas(150, 150);
                $cart->insert(public_path("storage/images/cards/cart.png"));
                $cart->resize(100, 100);

                $recover = Image::canvas(150, 150);
                $recover->insert(public_path("storage/images/cards/recover.png"));
                $recover->resize(160, 160);

                $image->insert($cart, "center");
                $image->insert($recover, "center");

            break;
            case "recover_light":

                $light = Image::canvas(150, 150);
                $light->insert(public_path("storage/images/cards/lantern.png"));
                $light->resize(100, 100);
                
                $recover = Image::canvas(150, 150);
                $recover->insert(public_path("storage/images/cards/recover.png"));
                $recover->resize(160, 160);

                $image->insert($light, "center");
                $image->insert($recover, "center");

            break;
            case "recover_pickaxe_cart":

                $pickaxe = Image::canvas(150, 150);
                $pickaxe->insert(public_path("storage/images/cards/pickaxe.png"));
                $pickaxe->resize(100, 100);

                $cart = Image::canvas(150, 150);
                $cart->insert(public_path("storage/images/cards/cart.png"));
                $cart->resize(100, 100);

                $recover = Image::canvas(150, 150);
                $recover->insert(public_path("storage/images/cards/recover.png"));
                $recover->resize(160, 160);

                $image->insert($pickaxe, "left", 50);
                $image->insert($cart, "right", 50);
                $image->insert($recover, "center");

            break;
            case "recover_pickaxe_light":

                $pickaxe = Image::canvas(150, 150);
                $pickaxe->insert(public_path("storage/images/cards/pickaxe.png"));
                $pickaxe->resize(100, 100);

                $light = Image::canvas(150, 150);
                $light->insert(public_path("storage/images/cards/lantern.png"));
                $light->resize(100, 100);

                $recover = Image::canvas(150, 150);
                $recover->insert(public_path("storage/images/cards/recover.png"));
                $recover->resize(160, 160);

                $image->insert($pickaxe, "left", 50);
                $image->insert($light, "right", 50);
                $image->insert($recover, "center");

            break;
            case "recover_light_cart":

                $light = Image::canvas(150, 150);
                $light->insert(public_path("storage/images/cards/lantern.png"));
                $light->resize(100, 100);

                $cart = Image::canvas(150, 150);
                $cart->insert(public_path("storage/images/cards/cart.png"));
                $cart->resize(100, 100);
                
                $recover = Image::canvas(150, 150);
                $recover->insert(public_path("storage/images/cards/recover.png"));
                $recover->resize(160, 160);
                
                $image->insert($light, "left", 50);
                $image->insert($cart, "right", 50);
                $image->insert($recover, "center");

            break;
            case "sabotage_pickaxe":
                
                $pickaxe = Image::canvas(150, 150);
                $pickaxe->insert(public_path("storage/images/cards/pickaxe.png"));
                $pickaxe->resize(100, 100);
                
                $sabotage = Image::canvas(160, 160);
                $sabotage->insert(public_path("storage/images/cards/disable.png"), "center");

                $image->insert($pickaxe, "center");
                $image->insert($sabotage, "center");

            break;
            case "sabotage_light":
                
                $light = Image::canvas(150, 150);
                $light->insert(public_path("storage/images/cards/lantern.png"));
                $light->resize(100, 100);

                $sabotage = Image::canvas(160, 160);
                $sabotage->insert(public_path("storage/images/cards/disable.png"), "center");
                
                $image->insert($light, "center");
                $image->insert($sabotage, "center");

            break;
            case "sabotage_cart":
                
                $cart = Image::canvas(150, 150);
                $cart->insert(public_path("storage/images/cards/cart.png"));
                $cart->resize(100, 100);

                $sabotage = Image::canvas(160, 160);
                $sabotage->insert(public_path("storage/images/cards/disable.png"), "center");

                $image->insert($cart, "center");
                $image->insert($sabotage, "center");

            break;
            case "sabotage_pickaxe_cart":
                
                $pickaxe = Image::canvas(150, 150);
                $pickaxe->insert(public_path("storage/images/cards/pickaxe.png"));
                $pickaxe->resize(100, 100);

                $cart = Image::canvas(150, 150);
                $cart->insert(public_path("storage/images/cards/cart.png"));
                $cart->resize(100, 100);
                
                $sabotage = Image::canvas(150, 150);
                $sabotage->insert(public_path("storage/images/cards/disable.png"));

                $image->insert($pickaxe, "left", 50);
                $image->insert($cart, "right", 50);
                $image->insert($sabotage, "center");

            break;
            case "sabotage_pickaxe_light":
                
                $pickaxe = Image::canvas(150, 150);
                $pickaxe->insert(public_path("storage/images/cards/pickaxe.png"));
                $pickaxe->resize(100, 100);
                
                $light = Image::canvas(150, 150);
                $light->insert(public_path("storage/images/cards/lantern.png"));
                $light->resize(100, 100);

                $sabotage = Image::canvas(150, 150);
                $sabotage->insert(public_path("storage/images/cards/disable.png"));

                $image->insert($pickaxe, "left", 50);
                $image->insert($light, "right", 50);
                $image->insert($sabotage, "center");

            break;
            case "sabotage_light_cart":
                
                $light = Image::canvas(150, 150);
                $light->insert(public_path("storage/images/cards/lantern.png"));
                $light->resize(100, 100);
                
                $cart = Image::canvas(150, 150);
                $cart->insert(public_path("storage/images/cards/cart.png"));
                $cart->resize(100, 100);

                $sabotage = Image::canvas(150, 150);
                $sabotage->insert(public_path("storage/images/cards/disable.png"));

                $image->insert($light, "left", 50);
                $image->insert($cart, "right", 50);
                $image->insert($sabotage, "center");

            break;
        }

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
