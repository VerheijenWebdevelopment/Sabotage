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

    public function generateRoles(Game $game)
    {
        $out = [];

        // Grab the available roles
        $saboteur = $this->findByName("saboteur");
        $digger = $this->findByName("digger");

        // Switch between number of players & determine number of each roles to generate
        switch ($game->players->count())
        {
            case 2:
                $num_diggers = 1;
                $num_saboteurs = 1;
            case 3:
                $num_diggers = 3;
                $num_saboteurs = 1;
                break;
            case 4:
                $num_diggers = 4;
                $num_saboteurs = 1;
                break;
            case 5:
                $num_diggers = 4;
                $num_saboteurs = 2;
                break;
            case 6:
                $num_diggers = 5;
                $num_saboteurs = 2;
                break;
            case 7:
                $num_diggers = 5;
                $num_saboteurs = 3;
                break;
            case 8:
                $num_diggers = 6;
                $num_saboteurs = 3;
                break;
            case 9:
                $num_diggers = 7;
                $num_saboteurs = 3;
                break;
            case 10:
                $num_diggers = 7;
                $num_saboteurs = 4;
                break;
            default:
                return [];
        }

        // Generate the pool of roles
        for ($i = 0; $i < $num_diggers; $i++) $out[] = $digger->id;
        for ($i = 0; $i < $num_saboteurs; $i++) $out[] = $saboteur->id;

        // Shuffle the pool so the roles are randomly divided
        shuffle($out);

        // Return the generated pool of role id's
        return $out;
    }

    public function countGeneratedRoles(array $roleIds)
    {
        $out = [];

        $total = 0;
        foreach ($this->getAll() as $role)
        {
            $count = 0;
            foreach ($roleIds as $roleId)
            {
                if ($role->id == $roleId)
                {
                    $count++;
                    $total++;
                }
            }
            $out[] = [
                "role_id" => $role->id,
                "count" => $count,
            ];
        }

        return $out;
    }
}
