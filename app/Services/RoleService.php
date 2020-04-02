<?php

namespace App\Services;

use App\Models\Role;
use App\Traits\ModelServiceGetters;
use App\Contracts\ModelServiceContract;

class RoleService implements ModelServiceContract
{
    use ModelServiceGetters;

    private $model;
    private $records;
    private $preloadedRecords;

    public function __construct()
    {
        $this->model =  "App\Models\Role";
    }

    public function preload($instance)
    {
        return $instance;
    }
}
