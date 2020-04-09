<?php

namespace App\Http\Controllers;

use Cards;
use Board;

class TestController extends Controller
{
    public function getTestCardGeneration()
    {
        return view("pages.tests.card-generation", [
            "cards" => Cards::getAll(),
        ]);
    }

    public function getTestArrayManip()
    {
        $b = [
            [null, null, null],
            [null, 1, null],
            [null, null, null],
        ];

        $c = Board::addRowToBoard($b, "top");
        $d = Board::addRowToBoard($b, "bottom");
        $e = Board::addColumnToBoard($b, "left");
        $f = Board::addColumnToBoard($b, "right");

        dd($b, $e, $f);
    }

    public function getTestReachedGold()
    {
        $board = [
            [
                null, null, null, null, null, null, null, null, null, null, null,
            ],
            [
                null, null, null, null, null,
                ["card_id" => 25, "inverted" => true],
                null, null, null, null, null,
            ],
            [
                null, null, null,
                ["card_id" => 26, "inverted" => true],
                null,
                ["card_id" => 26, "inverted" => true],
                null, null, null, null, null,
            ],
            [
                null, null, null,
                ["card_id" => 29, "inverted" => false],
                ["card_id" => 30, "inverted" => false],
                ["card_id" => 26, "inverted" => false],
                null, null, null,
                ["card_id" => 2, "inverted" => false],
                null,
            ],
            [
                null, null, null,
                ["card_id" => 27, "inverted" => false],
                null, null, null, null, null, null, null,
            ],
            [
                null,
                ["card_id" => 1, "inverted" => false],
                ["card_id" => 28, "inverted" => false],
                ["card_id" => 31, "inverted" => false],
                ["card_id" => 24, "inverted" => false],
                ["card_id" => 28, "inverted" => false],
                ["card_id" => 33, "inverted" => false],
                ["card_id" => 28, "inverted" => false],
                ["card_id" => 33, "inverted" => false],
                ["card_id" => 2, "inverted" => false],
                null,
            ],
            [
                null, null, null,
                ["card_id" => 32, "inverted" => false],
                ["card_id" => 31, "inverted" => false],
                null, null, null, null, null, null,
            ],
            [
                null, null, null, null,
                ["card_id" => 25, "inverted" => true],
                null, null, null, null,
                ["card_id" => 2, "inverted" => false],
                null,
            ],
            [
                null, null, null, null, null, null, null, null, null, null, null,
            ],
        ];

        $locations = Board::goldLocationsReached($board);

        dd($locations);
    }
}