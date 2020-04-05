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
}