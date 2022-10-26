<?php

declare(strict_types=1);

namespace App\Game\Environment;

use App\Parser\CellNotFoundException;

class World
{
    public function evolve(WorldState $worldState): WorldState
    {
        $wordSize = $worldState->getWorldSize();

        $newGeneration = [];
        for ($x = 0; $x < $wordSize; $x++) {
            for ($y = 0; $y < $wordSize; $y++) {
                try {
                    $cell = $worldState->getCellByPosition($x, $y);
                } catch (CellNotFoundException $e) {
                    continue;
                }
                $neighbours = $this->getCellNeighbors($worldState, $cell);
                $newGeneration[$x][$y] = $cell->evolve($neighbours);
            }
        }

        return WorldState::createWithNewGeneration($wordSize, $worldState->getIterations(), $newGeneration);
    }

    /**
     * @param WorldState $worldState
     * @param Cell $cell
     * @return Cell[]
     */
    private function getCellNeighbors(WorldState $worldState, Cell $cell): array
    {
        $minY = max(0, $cell->getY() - 1);
        $maxY = min($worldState->getWorldSize() - 1, $cell->getY() + 1);
        $minX = max(0, $cell->getX() - 1);
        $maxX = min($worldState->getWorldSize() - 1, $cell->getX() + 1);

        $neighbours = [];
        for ($x = $minX; $x <= $maxX; $x++) {
            for ($y = $minY; $y <= $maxY; $y++) {
                if ($x === $cell->getX() && $y === $cell->getY()) {
                    continue;
                }

                try {
                    $neighbour = $worldState->getCellByPosition($x, $y);
                } catch (CellNotFoundException $e) {
                    continue;
                }

                $neighbours[] = $neighbour;
            }
        }

        return $neighbours;
    }
}
