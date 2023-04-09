<?php

declare(strict_types=1);

namespace App\GameOfLife\DTO;

class LifeFactory
{
    /**
     * @param array{
     *     world:array{cells:string, iterations:string},
     *     organisms:array{array{x_pos:string, y_pos:string}}
     *     } $data
     * @return Life
     */
    public function create(array $data): Life
    {
        $world = new World(
            (int) $data[Life::FIELD_WORLD][World::FIELD_CELLS],
            (int) $data[Life::FIELD_WORLD][World::FIELD_ITERATIONS]
        );

        $organisms = [];
        foreach ($data[Life::FIELD_ORGANISMS] as $organism) {
            $organisms[] = new Organism(
                (int) $organism[Organism::FIELD_POSITION_X],
                (int) $organism[Organism::FIELD_POSITION_Y]
            );
        }

        return new Life($world, $organisms);
    }
}
