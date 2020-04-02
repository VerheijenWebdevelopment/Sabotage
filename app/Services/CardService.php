<?php

namespace App\Services;

use App\Models\Card;
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
        return $instance;
    }
}
