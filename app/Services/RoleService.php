<?php

namespace App\Services;

use App\Models\Game;
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

    public function findByName($name)
    {
        foreach ($this->getAll() as $role)
        {
            if ($role->name == $name)
            {
                return $role;
            }
        }

        return false;
    }

    public function generateRoleDeck()
    {
        // Grab all available roles
        $saboteur = $this->findByName("saboteur");
        $blue_digger = $this->findByName("blue_digger");
        $green_digger = $this->findByName("green_digger");
        $chef = $this->findByName("chef");
        $profiteer = $this->findByName("profiteer");
        $geologist = $this->findByName("geologist");

        // Compose the "deck" of possible roles
        $out = [
            $saboteur->id,
            $saboteur->id,
            $saboteur->id,
            $blue_digger->id,
            $blue_digger->id,
            $blue_digger->id,
            $blue_digger->id,
            $green_digger->id,
            $green_digger->id,
            $green_digger->id,
            $green_digger->id,
            $chef->id,
            $profiteer->id,
            $geologist->id,
            $geologist->id,
        ];

        // Shuffle the deck a few times to make the order random
        shuffle($out);
        shuffle($out);

        // Return the generated pool of role id's
        return $out;
    }

    public function countGeneratedRoles(array $roleIds)
    {
        // Array we're outputting
        $out = [];

        // Count the total number of roles that are available
        $total = 0;

        // Loop through all of the roles
        foreach ($this->getAll() as $role)
        {
            // Count the number of times this role is available
            $count = 0;

            // Process all of the role id's (representing role cards) in the deck
            foreach ($roleIds as $roleId)
            {
                if ($role->id == $roleId)
                {
                    $count++;
                    $total++;
                }
            }
            // Add the entry for this role
            $out[] = [
                "role_id" => $role->id,
                "count" => $count,
            ];
        }

        // Return the output
        return $out;
    }
}
