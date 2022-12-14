<?php

declare(strict_types=1);

namespace App\Game\Environment;

use App\Game\Input\Life;
use App\Parser\CellNotFoundException;

class WorldState
{
    /**
     * @var Cell[][] List of Cells indexed by its positions
     */
    private array $cells;

    private int $worldSize;

    /**
     * @param int $worldSize
     * @param Cell[][] $cells List of Cells indexed by its positions
     */
    private function __construct(int $worldSize, array $cells)
    {
        $this->cells = $cells;
        $this->worldSize = $worldSize;
    }

    /**
     * @throws CellNotFoundException
     */
    public function getCellByPosition(int $x, int $y): Cell
    {
        if (isset($this->cells[$x][$y]) === false) {
            throw new CellNotFoundException();
        }

        return $this->cells[$x][$y];
    }

    public function getWorldSize(): int
    {
        return $this->worldSize;
    }

    public static function createFromLife(Life $life): self
    {
        $livingCells = [];
        foreach ($life->getOrganisms() as $organism) {
            $livingCells[$organism->getXPosition()][$organism->getYPosition()] = true;
        }

        $worldSize = $life->getWorld()->getCells();
        $cells = [];
        for ($x = 0; $x < $worldSize; $x++) {
            for ($y = 0; $y < $worldSize; $y++) {
                $cells[$x][$y] = new Cell($x, $y, isset($livingCells[$x][$y]));
            }
        }

        return new self($worldSize, $cells);
    }

    /**
     * @param int $worldSize
     * @param Cell[][] $newGeneration List of Cells indexed by its positions
     * @return WorldState
     */
    public static function createWithNewGeneration(int $worldSize, array $newGeneration): WorldState
    {
        return new self($worldSize, $newGeneration);
    }
}
