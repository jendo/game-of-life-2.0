<?php

declare(strict_types=1);

namespace App\Game\Output;

use App\Game\Environment\WorldState;
use App\Game\Input\Life;
use App\Game\Input\Organism;
use App\Game\Input\World;
use Sabre\Xml\Service;

class XmlOutputProvider
{
    private Service $xmlService;

    public function __construct(Service $xmlService)
    {
        $this->xmlService = $xmlService;
    }

    public function provide(WorldState $worldState, string $namespace = '', string $prefix = ''): string
    {
        if ($namespace !== '') {
            $this->xmlService->namespaceMap = [
                $namespace => $prefix,
            ];
        }

        $organisms = [];
        foreach ($worldState->getLivingCellList() as $cell) {
            $organisms[] = [
                $this->getFullElementName(Organism::NAME, $namespace) => [
                    $this->getFullElementName(Organism::FIELD_POSITION_X, $namespace) => $cell->getX(),
                    $this->getFullElementName(Organism::FIELD_POSITION_Y, $namespace) => $cell->getY(),
                ]
            ];
        }

        return $this->xmlService->write(
            $this->getFullElementName(Life::NAME, $namespace),
            [
                $this->getFullElementName(Life::FIELD_WORLD, $namespace) => [
                    $this->getFullElementName(World::FIELD_CELLS, $namespace) => $worldState->getWorldSize(),
                    $this->getFullElementName(World::FIELD_ITERATIONS, $namespace) => $worldState->getIterations(),
                ],
                $this->getFullElementName(Life::FIELD_ORGANISMS, $namespace) => $organisms
            ]
        );
    }

    private function getFullElementName(string $element, string $namespace): string
    {
        if ($namespace === '') {
            return $element;
        }

        return sprintf('{%s}%s', $namespace, $element);
    }
}
