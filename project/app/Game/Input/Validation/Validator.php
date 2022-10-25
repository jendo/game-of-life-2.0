<?php

declare(strict_types=1);

namespace App\Game\Input\Validation;

use App\Game\Input\Life;
use App\Game\Input\Organism;
use App\Game\Input\World;
use ReflectionClass;

class Validator
{
    /**
     * @var Error[]
     */
    private array $errors = [];


    /**
     * @param array<string, array<int|string, array<string, string>|string>|string> $data
     * @return void
     * @throws InvalidDataException
     */
    public function validate(array $data): void
    {
        $this->validateWorld($data);
        $this->validateOrganisms($data);

        if ($this->errors !== []) {
            throw InvalidDataException::createFromErrors($this->errors);
        }
    }

    /**
     * @return Error[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array<string, array<int|string, array<string, string>|string>|string> $data
     * @return void
     */
    private function validateWorld(array $data): void
    {
        if (isset($data[Life::FIELD_WORLD]) === false) {
            $this->errors[] = new Error(Error::MISSING_FIELD_MESSAGE, Life::FIELD_WORLD);

            return;
        }

        if (is_array($data[Life::FIELD_WORLD]) === false) {
            $this->errors[] = new Error(Error::INVALID_FIELD_TYPE_MESSAGE, Life::FIELD_WORLD);

            return;
        }


        if (isset($data[Life::FIELD_WORLD][World::FIELD_CELLS]) === false) {
            $this->errors[] = new Error(Error::MISSING_FIELD_MESSAGE, World::FIELD_CELLS);
        }

        if (isset($data[Life::FIELD_WORLD][World::FIELD_ITERATIONS]) === false) {
            $this->errors[] = new Error(Error::MISSING_FIELD_MESSAGE, World::FIELD_ITERATIONS);
        }
    }


    /**
     * @param array<string, array<int|string, array<string, string>|string>|string> $data
     * @return void
     */
    private function validateOrganisms(array $data): void
    {
        if (isset($data[Life::FIELD_ORGANISMS]) === false) {
            $this->errors[] = new Error(Error::MISSING_FIELD_MESSAGE, Life::FIELD_ORGANISMS);

            return;
        }

        if (is_array($data[Life::FIELD_ORGANISMS]) === false) {
            $this->errors[] = new Error(Error::INVALID_FIELD_TYPE_MESSAGE, Life::FIELD_ORGANISMS);

            return;
        }

        if ($data[Life::FIELD_ORGANISMS] === []) {
            $this->errors[] = new Error(Error::EMPTY_FIELD_MESSAGE, Life::FIELD_ORGANISMS);
        }

        foreach ($data[Life::FIELD_ORGANISMS] as $organism) {
            if (is_array($organism) === false) {
                $this->errors[] = new Error(
                    Error::INVALID_FIELD_TYPE_MESSAGE,
                    (new ReflectionClass(Organism::class))->getShortName()
                );
                continue;
            }

            if ($organism === []) {
                $this->errors[] = new Error(
                    Error::EMPTY_FIELD_MESSAGE,
                    (new ReflectionClass(Organism::class))->getShortName()
                );
                continue;
            }

            if (isset($organism[Organism::FIELD_POSITION_Y]) === false) {
                $this->errors[] = new Error(Error::MISSING_FIELD_MESSAGE, Organism::FIELD_POSITION_Y);
            }

            if (isset($organism[Organism::FIELD_POSITION_X]) === false) {
                $this->errors[] = new Error(Error::MISSING_FIELD_MESSAGE, Organism::FIELD_POSITION_X);
            }
        }
    }
}
