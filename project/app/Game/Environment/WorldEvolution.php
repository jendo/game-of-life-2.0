<?php

declare(strict_types=1);

namespace App\Game\Environment;

use App\Game\Input\Life;

class WorldEvolution
{
    /**
     * @param Life $life
     * @return WorldState[]
     */
    public function start(Life $life): array
    {
        $worldState = WorldState::createFromLife($life);

        $worldStates = [];
        for ($i = 0; $i < $life->getWorld()->getIterations(); $i++) {
            $worldState = (new World())->evolve($worldState);
            $worldStates[] = $worldState;
        }

        return  $worldStates;
    }
}
