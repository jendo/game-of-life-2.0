<?php

declare(strict_types=1);

namespace App\GameOfLife\DTO;

class Life
{
    public const FIELD_WORLD = 'world';
    public const FIELD_ORGANISMS = 'organisms';

    private World $world;

    /**
     * @var Organism[]
     */
    private array $organisms;

    /**
     * @param World $world
     * @param Organism[] $organisms
     */
    public function __construct(World $world, array $organisms)
    {
        $this->world = $world;
        $this->organisms = $organisms;
    }
    public function getWorld(): World
    {
        return $this->world;
    }

    /**
     * @return Organism[]
     */
    public function getOrganisms(): array
    {
        return $this->organisms;
    }
}
