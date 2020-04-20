<?php

namespace App\Services;

use Log;
use Cards;

use App\Models\Card;
use App\Models\Game;

class BoardService
{
    public function generateBoard(Game $game)
    {
        // Array representing the game board
        $board = [];

        // Define the number of cells for both axis
        $width = 11;
        $height = 7;

        // Grab the start & gold location card so we can place it during generation
        $startCard = Cards::findByName("start");
        $goldCard = Cards::findByName("gold_location");

        // Generate the board
        for ($y = 0; $y < $height; $y++) {
            $row = [];
            for ($x = 0; $x < $width; $x++) {
                // If this is the start location
                if ($x == 1 && $y == 3) {
                    $row[] = ["card_id" => $startCard->id, "inverted" => false];
                // If this is the first gold location
                } else if ($x == 9 && $y == 1) {
                    $row[] = ["card_id" => $goldCard->id, "inverted" => false];
                // If this is the second gold location
                } else if ($x == 9 && $y == 3) {
                    $row[] = ["card_id" => $goldCard->id, "inverted" => false];
                // If this is the third gold location
                } else if ($x == 9 && $y == 5) {
                    $row[] = ["card_id" => $goldCard->id, "inverted" => false];
                // If this is any other tile on the grid
                } else {
                    $row[] = null;
                }
            }
            $board[] = $row;
        }

        // Return generated board
        return $board;
    }

    public function placeCard(Game $game, Card $card, bool $inverted, $x, $y)
    {
        // Grab the current state of the board
        $board = $game->currentRound->board;

        // Add the card to the board & save the new board on the game
        $board[$y][$x] = [
            "card_id" => $card->id,
            "inverted" => $inverted,   
        ];

        // If we placed a card on the top row; add a row at the top of the board
        if ($y == 0) $board = $this->addRowToBoard($board, "top");

        // If we placed a card on the bottom row; add a row at the bottom of the board
        if ($y == (count($board) - 1)) $board = $this->addRowToBoard($board, "bottom");

        // If we placed the card on the most left row; add a column to the left of the board
        if ($x == 0) $board = $this->addColumnToBoard($board, "left");

        // If we placed the card on the most right row; add a column to the right of the board
        if ($x == (count($board[0]) -1)) $board = $this->addColumnToBoard($board, "right");

        // Save the updated board on the game
        $game->currentRound->board = $board;
        $game->currentRound->save();

        // Return updated game
        return $board;
    }

    public function addRowToBoard(array $board, string $direction)
    {
        // Determine the width of the board by counting the columns in the first row
        $width = count($board[0]);

        // Generate an empty row
        $row = [];
        for ($i = 0; $i < $width; $i++) $row[] = null;

        // If we're adding a row to the top of the board
        if ($direction == "top")
        {
            // Start the new board with the new row
            $new_board = [$row];
            
            // Loop through the current board and add each row to the new board
            for ($i = 0; $i < count($board); $i++) $new_board[] = $board[$i];
        }
        // If we're adding a row to the bottom of the board
        else
        {
            $new_board = $board;
            $new_board[] = $row;
        }

        // Return updated board
        return $new_board;
    }

    public function addColumnToBoard(array $board, string $direction)
    {
        // If we're adding a column to the left of the board
        if ($direction == "left")
        {
            // Start the new board empty
            $new_board = [];

            // Loop through all of the current board's rows
            for ($y = 0; $y < count($board); $y++)
            {
                // Prepend an entry to the beginning of this row
                array_unshift($board[$y], null);

                // Add the row to the new board
                $new_board[] = $board[$y];
            }
        }
        // If we're adding a column to the right of the board
        else
        {
            // Start the new board by copying the current board
            $new_board = $board;

            // Loop through all of the columns
            for($y = 0; $y < count($board); $y++)
            {
                // And add an entry to the end of the row
                $new_board[$y][] = null;
            }
        }

        // Return updated board
        return $new_board;
    }

    public function goldLocationsReached(array $board)
    {
        // Grab coordinates of start card and gold locations
        $startCoordinates = $this->findCardCoordsByName("start", $board);
        $goldLocationCoordinates = $this->determineGoldLocationCoords($startCoordinates);

        // Loop through each of the gold locations
        $reachedGoldLocations = [];
        foreach ($goldLocationCoordinates as $number => $coords)
        {
            $reached_target = $this->traverseToTarget($startCoordinates, null, $coords, $board);
            if ($reached_target)
            {
                $reachedGoldLocations[] = $number;
            }
        }

        // Return the gold locations that have been reached
        return $reachedGoldLocations;
    }

    private function findCardCoordsByName($name, array $board)
    {
        $card = Cards::findByName($name);

        for ($rowIndex = 0; $rowIndex < count($board); $rowIndex++)
        {
            for ($columnIndex = 0; $columnIndex < count($board[$rowIndex]); $columnIndex++)
            {
                if ($board[$rowIndex][$columnIndex] != null && $board[$rowIndex][$columnIndex]["card_id"] == $card->id)
                {
                    return [
                        "rowIndex" => $rowIndex, 
                        "columnIndex" => $columnIndex
                    ];
                }
            }
        }

        return false;
    }

    private function determineGoldLocationCoords($startCoords)
    {
        return [
            1 => [
                "rowIndex" => $startCoords["rowIndex"] - 2,
                "columnIndex" => $startCoords["columnIndex"] + 8,
            ],
            2 => [
                "rowIndex" => $startCoords["rowIndex"],
                "columnIndex" => $startCoords["columnIndex"] + 8,
            ],
            3 => [
                "rowIndex" => $startCoords["rowIndex"] + 2,
                "columnIndex" => $startCoords["columnIndex"] + 8,
            ],
        ];
    }

    private function traverseToTarget($coords, $prevCoords, $targetCoords, $board)
    {
        // Grab the card on the current tile's coordinates
        $card = Card::find($board[$coords["rowIndex"]][$coords["columnIndex"]]["card_id"]);
        $inverted = $board[$coords["rowIndex"]][$coords["columnIndex"]]["inverted"];
        
        // Determine which tiles are connected (by tunnel) and save their coordinates & direction we're coming from
        $connected_tiles = [];
        if (is_array($card->open_positions) && count($card->open_positions))
        {
            foreach ($card->open_positions as $open_position)
            {
                switch ($open_position)
                {
                    case "top":
                        if (!$inverted) {
                            $connected_tiles[] = ["rowIndex" => $coords["rowIndex"] - 1, "columnIndex" => $coords["columnIndex"]];
                        } else {
                            $connected_tiles[] = ["rowIndex" => $coords["rowIndex"] + 1, "columnIndex" => $coords["columnIndex"]];
                        }
                    break;
                    case "right":
                        if (!$inverted) {
                            $connected_tiles[] = ["rowIndex" => $coords["rowIndex"], "columnIndex" => $coords["columnIndex"] + 1];
                        } else {
                            $connected_tiles[] = ["rowIndex" => $coords["rowIndex"], "columnIndex" => $coords["columnIndex"] - 1];
                        }
                    break;
                    case "bottom":
                        if (!$inverted) {
                            $connected_tiles[] = ["rowIndex" => $coords["rowIndex"] + 1, "columnIndex" => $coords["columnIndex"]];
                        } else {
                            $connected_tiles[] = ["rowIndex" => $coords["rowIndex"] - 1, "columnIndex" => $coords["columnIndex"]];
                        }
                    break;
                    case "left":
                        if (!$inverted) {
                            $connected_tiles[] = ["rowIndex" => $coords["rowIndex"], "columnIndex" => $coords["columnIndex"] - 1];
                        } else {
                            $connected_tiles[] = ["rowIndex" => $coords["rowIndex"], "columnIndex" => $coords["columnIndex"] + 1];
                        }
                    break;
                }
            }
        }

        // If one of the connecting tiles is the tile we're targetting, return true!
        foreach ($connected_tiles as $connected_tile)
        {
            // Log::debug("Connected tile: ".$connected_tile["rowIndex"].",".$connected_tile["columnIndex"]." (target: ".$targetCoords["rowIndex"].",".$targetCoords["columnIndex"].")");
            if ($connected_tile["rowIndex"] == $targetCoords["rowIndex"] && $connected_tile["columnIndex"] == $targetCoords["columnIndex"])
            {
                return true;
            }
        }

        // Determine which of the connected tiles has a card on it
        $connected_tunnel_tiles = [];
        foreach ($connected_tiles as $connected_tile)
        {
            // If this is the previous tile we were on; skip it
            if ($this->tileHasBeenTraversed($connected_tile, $prevCoords)) continue;

            // If there is a card on the connected tile; it's a tunnel card jaj
            if (!is_null($board[$connected_tile["rowIndex"]][$connected_tile["columnIndex"]]))
            {
                $connected_tunnel_tiles[] = $connected_tile;
            }
        }

        // If we've reached this point the connected tiles are not the target tile; so lets' keep on searching
        $prevCoords[] = $coords;
        foreach ($connected_tunnel_tiles as $connected_tunnel_tile)
        {
            if ($this->traverseToTarget($connected_tunnel_tile, $prevCoords, $targetCoords, $board))
            {
                return true;
            }
        }
        
        // If we've reached this point, return false
        return false;
    }

    private function tileHasBeenTraversed($coords, $prevCoords)
    {
        if (is_array($prevCoords) && count($prevCoords))
        {
            foreach ($prevCoords as $prevCoord)
            {
                if ($prevCoord["rowIndex"] == $coords["rowIndex"] && $prevCoord["columnIndex"] == $coords["columnIndex"])
                {
                    return true;
                }
            }
        }

        return false;
    }

    private function cardIsConnected($direction, $card, $inverted)
    {
        switch ($direction)
        {
            case "top": // so current card is below
                if ($inverted) {
                    return array_key_exists("top", $card->open_positions);
                } else {
                    return array_key_exists("bottom", $card->open_positions);
                }
            break;
            case "right": // so current card is to the left
                if ($inverted) {
                    return array_key_exists("right", $card->open_positions);
                } else {
                    return array_key_exists("left", $card->open_positions);    
                }
            break;
            case "bottom": // so current card is above
                if ($inverted) {
                    return array_key_exists("bottom", $card->open_positions);
                } else {
                    return array_key_exists("top", $card->open_positions);
                }
            break;
            case "left": // so current is to the right
                if ($inverted) {
                    return array_key_exists("left", $card->open_positions);
                } else {
                    return array_key_exists("right", $card->open_positions);
                }
            break;
        }

        return false;
    }

    public function revealGoldLocation(Game $game, int $goldLocation)
    {
        // Determine if the revealed gold locations contains gold or not
        $contains_gold = $goldLocation == $game->gold_location ? true : false;

        // Add the gold location to the list of revealed gold locations
        $reachedGoldLocations = $game->reached_gold_locations;
        $reachedGoldLocations[$goldLocation] = $contains_gold;
        $game->reached_gold_locations = $reachedGoldLocations;

        // Determine the coordinates of the start card & all of the gold location cards
        $startCardCoords = $this->findCardCoordsByName("start", $game->board);
        $goldCardCoords = $this->determineGoldLocationCoords($startCardCoords);

        // Grab the coal & gold cards
        $coalCard = Cards::findByName("coal");
        $goldCard = Cards::findByName("gold");

        // Grab the coordinates of the revealed gold location
        $goldLocationCoords = $goldCardCoords[$goldLocation];

        // Update the gold location's card on the board
        $board = $game->board;
        $board[$goldLocationCoords["rowIndex"]][$goldLocationCoords["columnIndex"]] = [
            "card_id" => $contains_gold ? $goldCard->id : $coalCard->id,
            "inverted" => false,
        ];
        $game->board = $board;

        // Save changes we made to the game
        $game->save();

        // Return the updated game
        return $game;
    }
}