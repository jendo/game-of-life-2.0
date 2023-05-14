<?php

declare(strict_types=1);

namespace App\Game\Environment;

class Rule
{
    private string $cellState;
    private int $numberOfLivingNeighbors;

    /**
     * @param string $cellState
     * @param int $numberOfLivingNeighbors
     * @param string $initialState
     */
    public function __construct(string $cellState, int $numberOfLivingNeighbors)
    {
        $this->cellState = $cellState;
        $this->numberOfLivingNeighbors = $numberOfLivingNeighbors;
    }


    public function isForLiveCell(): bool
    {
    }

    public function getInitialState():string
    {

    }

}
