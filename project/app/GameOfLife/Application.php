<?php

declare(strict_types=1);

namespace App\GameOfLife;

use App\GameOfLife\DTO\Life;
use App\GameOfLife\Environment\World;
use App\GameOfLife\Environment\WorldState;

class Application
{
    /**
     * @param Life $life
     * @return WorldState[]
     */
    public function run(Life $life): array
    {
        $worldState = WorldState::createFromLife($life);

        $worldStates = [];
        for ($i = 0; $i < $life->getWorld()->getIterations(); $i++) {
            $worldState = (new World())->evolve($worldState);
            $worldStates[] = $worldState;
        }

        return $worldStates;
    }
}
