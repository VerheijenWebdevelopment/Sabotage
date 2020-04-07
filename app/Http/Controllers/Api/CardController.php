<?php

namespace App\Http\Controllers\Api;

use Image;
use Cards;

use App\Models\Card;
use App\Http\Controllers\Controller;

class CardController extends Controller
{
    public function getGenerateImage($id)
    {
        // Image dimensions
        $w = 260;
        $h = 400;

        // Create a new canvas to paint our art on
        $image = Image::canvas($w, $h, "#333");

        // Find the card we want to generate the image of
        $card = Cards::find($id);

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

        // Output the generated image as a JPG
        return $image->response();
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

    private function addTunnel(\Intervention\Image\Image $image, Card $card)
    {
        // Image dimensions
        $w = 260;
        $h = 400;

        // Grid dimensions
        $gw = $w / 3;
        $gh = $h / 3;

        // Draw the center tile for all tunnel types
        $image->rectangle($gw, $gh, ($gw*2), ($gh*2), function($draw) {
            $draw->background("#7a4600");
        });

        // Draw all the open position tiles
        foreach ($card->open_positions as $position)
        {
            switch ($position)
            {
                case "top":
                    $image->rectangle($gw, 0, ($gw*2), $gh, function($draw) {
                        $draw->background("#7a4600");
                    });
                break;
                case "right":
                    $image->rectangle(($gw*2), $gh, ($gw*3), ($gh*2), function($draw) {
                        $draw->background("#7a4600");
                    });
                break;
                case "bottom":
                    $image->rectangle($gw, ($gh*2), ($gw*2), ($gh*3), function($draw) {
                        $draw->background("#7a4600");
                    });
                break;
                case "left":
                    $image->rectangle(0, $gh, $gw, ($gh*2), function($draw) {
                        $draw->background("#7a4600");
                    });
                break;
            }
        }

        // Return the updated image
        return $image;
    }
}