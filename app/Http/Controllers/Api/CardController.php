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
        $image = Cards::generateCardImage($id);
        return $image->response();
    }
}