<?php

declare(strict_types=1);

namespace App\Game\Input;

class Organism
{
    public const FIELD_POSITION_X = 'x_pos';
    public const FIELD_POSITION_Y = 'y_pos';

    private int $xPosition;

    private int $yPosition;

    public function __construct(int $xPosition, int $yPosition)
    {
        $this->xPosition = $xPosition;
        $this->yPosition = $yPosition;
    }

    public function getXPosition(): int
    {
        return $this->xPosition;
    }

    public function getYPosition(): int
    {
        return $this->yPosition;
    }
}
