<?php

declare(strict_types=1);

namespace App\Game\Input;

class World
{
    public const FIELD_CELLS = 'cells';
    public const FIELD_ITERATIONS = 'iterations';

    private int $cells;

    private int $iterations;

    public function __construct(int $cells, int $iterations)
    {
        $this->cells = $cells;
        $this->iterations = $iterations;
    }

    public function getCells(): int
    {
        return $this->cells;
    }

    public function getIterations(): int
    {
        return $this->iterations;
    }
}
