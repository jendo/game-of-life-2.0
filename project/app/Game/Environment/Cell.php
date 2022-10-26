<?php

declare(strict_types=1);

namespace App\Game\Environment;

class Cell
{
    private int $x;

    private int $y;

    private bool $isAlive;

    public function __construct(int $x, int $y, bool $isAlive)
    {
        $this->x = $x;
        $this->y = $y;
        $this->isAlive = $isAlive;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function isAlive(): bool
    {
        return $this->isAlive;
    }

    /**
     * @param Cell[] $neighbours
     * @return Cell
     */
    public function evolve(array $neighbours): Cell
    {
        $liveNeighbors = 0;
        foreach ($neighbours as $neighbour) {
            if ($neighbour->isAlive()) {
                $liveNeighbors++;
            }
        }

        if ($this->isAlive()) {
            switch ($liveNeighbors) {
                case 2:
                case 3:
                    return $this;
                default:
                    return new Cell($this->x, $this->y, false);
            }
        } else {
            switch ($liveNeighbors) {
                case 3:
                    return new Cell($this->x, $this->y, true);
                default:
                    return new Cell($this->x, $this->y, false);
            }
        }
    }
}
