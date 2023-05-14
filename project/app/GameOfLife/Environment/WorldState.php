<?php

declare(strict_types=1);

namespace App\GameOfLife\Environment;

use App\GameOfLife\DTO\Life;

class WorldState
{
    private int $worldSize;

    private int $iterations;

    /**
     * @var Cell[][] List of Cells indexed by its positions
     */
    private array $cells;

    /**
     * @param int $worldSize
     * @param int $iterations
     * @param Cell[][] $cells List of Cells indexed by its positions
     */
    private function __construct(int $worldSize, int $iterations, array $cells)
    {
        $this->worldSize = $worldSize;
        $this->iterations = $iterations;
        $this->cells = $cells;
    }

    public function getWorldSize(): int
    {
        return $this->worldSize;
    }

    public function getIterations(): int
    {
        return $this->iterations;
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

    public static function createFromLife(Life $life): self
    {
        $livingCells = [];
        foreach ($life->getOrganisms() as $organism) {
            $livingCells[$organism->getXPosition()][$organism->getYPosition()] = true;
        }

        $worldSize = $life->getWorld()->getCells();
        $iterations = $life->getWorld()->getIterations();
        $cells = [];
        for ($x = 0; $x < $worldSize; $x++) {
            for ($y = 0; $y < $worldSize; $y++) {
                $cells[$x][$y] = new Cell($x, $y, isset($livingCells[$x][$y]));
            }
        }

        return new self($worldSize, $iterations, $cells);
    }

    /**
     * @param int $worldSize
     * @param int $iterations
     * @param Cell[][] $newGeneration List of Cells indexed by its positions
     * @return WorldState
     */
    public static function createWithNewGeneration(int $worldSize, int $iterations, array $newGeneration): WorldState
    {
        return new self($worldSize, $iterations, $newGeneration);
    }
}
