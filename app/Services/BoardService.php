<?php

namespace App\Services;

use Cards;
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
                    $row[] = $startCard->id;
                // If this is the first gold location
                } else if ($x == 9 && $y == 1) {
                    $row[] = $goldCard->id;
                // If this is the second gold location
                } else if ($x == 9 && $y == 3) {
                    $row[] = $goldCard->id;
                // If this is the third gold location
                } else if ($x == 9 && $y == 5) {
                    $row[] = $goldCard->id;
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

    public function placeCard(Game $game, Card $card, $x, $y)
    {
        // Grab the current state of the board
        $board = $game->board;

        // Add the card to the board & save the new board on the game
        $board[$x][$y] = $card->id;

        // If we placed a card on the top row; add a row at the top of the board
        if ($y == 0) $board = $this->addRowToBoard($board, "top");

        // If we placed a card on the bottom row; add a row at the bottom of the board
        if ($y == (count($board) - 1)) $board = $this->addRowToBoard($board, "bottom");

        // If we placed the card on the most left row; add a column to the left of the board
        if ($x == 0) $board = $this->addColumnToBoard($board, "left");

        // If we placed the card on the most right row; add a column to the right of the board
        if ($x == (count($board[0]) -1)) $board = $this->addColumnToBoard($board, "right");

        // Save the updated board on the game
        $game->board = $board;
        $game->save();

        // Return updated game
        return $game;
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
}