<?php

namespace App\Http\Controllers;

use Cards;

class TestController extends Controller
{
    public function getTestCardGeneration()
    {
        return view("pages.tests.card-generation", [
            "cards" => Cards::getAll(),
        ]);
    }
}