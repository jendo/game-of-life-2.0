<?php

declare(strict_types=1);

namespace App\Game\Input;

use App\Game\Input\Validation\InvalidDataException;
use App\Game\Input\Validation\Validator;

class LifeFactory
{
    private Validator $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array{
     *     world:array{cells:string, iterations:string},
     *     organisms:array{array{x_pos:string, y_pos:string}}
     *     } $data
     * @return Life
     * @throws InvalidDataException
     */
    public function create(array $data): Life
    {
        $this->validator->validate($data);

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
