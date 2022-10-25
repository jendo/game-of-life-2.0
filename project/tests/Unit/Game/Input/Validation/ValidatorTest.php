<?php

declare(strict_types=1);

namespace AppTest\Unit\Game\Input\Validation;

use App\Game\Input\Life;
use App\Game\Input\Organism;
use App\Game\Input\Validation\Error;
use App\Game\Input\Validation\InvalidDataException;
use App\Game\Input\Validation\Validator;
use App\Game\Input\World;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use ReflectionClass;

class ValidatorTest extends TestCase
{
    use ProphecyTrait;

    private Validator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new Validator();
    }

    /**
     * @throws InvalidDataException
     */
    public function testValidateWithoutErrors(): void
    {
        $data = [
            Life::FIELD_WORLD => [
                World::FIELD_CELLS => 10,
                World::FIELD_ITERATIONS => 100,
            ],
            Life::FIELD_ORGANISMS => [
                [
                    Organism::FIELD_POSITION_X => 10,
                    Organism::FIELD_POSITION_Y => 10,
                ],
            ],
        ];

        $this->validator->validate($data);

        self::assertSame([], $this->validator->getErrors(), 'Validator should not return any error.');
    }


    public function getInvalidData(): array
    {
        return [
            'missingWorld' => [
                'data' => [
                    Life::FIELD_ORGANISMS => [
                        [
                            Organism::FIELD_POSITION_X => 10,
                            Organism::FIELD_POSITION_Y => 10,
                        ],
                    ],
                ],
                'expectedErrors' => [
                    new Error(Error::MISSING_FIELD_MESSAGE, Life::FIELD_WORLD),
                ],
            ],
            'invalidWorldType' => [
                'data' => [
                    Life::FIELD_ORGANISMS => [
                        [
                            Organism::FIELD_POSITION_X => 10,
                            Organism::FIELD_POSITION_Y => 10,
                        ],
                    ],
                    Life::FIELD_WORLD => 'not array',
                ],
                'expectedErrors' => [
                    new Error(Error::INVALID_FIELD_TYPE_MESSAGE, Life::FIELD_WORLD),
                ],
            ],
            'worldIsMissingAllChildElements' => [
                'data' => [
                    Life::FIELD_ORGANISMS => [
                        [
                            Organism::FIELD_POSITION_X => 10,
                            Organism::FIELD_POSITION_Y => 10,
                        ],
                    ],
                    Life::FIELD_WORLD => [],
                ],
                'expectedErrors' => [
                    new Error(Error::MISSING_FIELD_MESSAGE, World::FIELD_CELLS),
                    new Error(Error::MISSING_FIELD_MESSAGE, World::FIELD_ITERATIONS),
                ],
            ],
            'missingOrganisms' => [
                'data' => [
                    Life::FIELD_WORLD => [
                        World::FIELD_CELLS => 10,
                        World::FIELD_ITERATIONS => 10,
                    ],
                ],
                'expectedErrors' => [
                    new Error(Error::MISSING_FIELD_MESSAGE, Life::FIELD_ORGANISMS),
                ],
            ],
            'invalidOrganismsType' => [
                'data' => [
                    Life::FIELD_WORLD => [
                        World::FIELD_CELLS => 10,
                        World::FIELD_ITERATIONS => 10,
                    ],
                    Life::FIELD_ORGANISMS => 'not array',
                ],
                'expectedErrors' => [
                    new Error(Error::INVALID_FIELD_TYPE_MESSAGE, Life::FIELD_ORGANISMS),
                ],
            ],
            'organismsIsEmpty' => [
                'data' => [
                    Life::FIELD_WORLD => [
                        World::FIELD_CELLS => 10,
                        World::FIELD_ITERATIONS => 10,
                    ],
                    Life::FIELD_ORGANISMS => [

                    ],
                ],
                'expectedErrors' => [
                    new Error(Error::EMPTY_FIELD_MESSAGE, Life::FIELD_ORGANISMS),
                ],
            ],
            'organismsHasChildWithInvalidType' => [
                'data' => [
                    Life::FIELD_WORLD => [
                        World::FIELD_CELLS => 10,
                        World::FIELD_ITERATIONS => 10,
                    ],
                    Life::FIELD_ORGANISMS => [
                        'invalid organism',
                    ],
                ],
                'expectedErrors' => [
                    new Error(
                        Error::INVALID_FIELD_TYPE_MESSAGE,
                        (new ReflectionClass(Organism::class))->getShortName()
                    ),
                ],
            ],
            'organismsHasEmptyChild' => [
                'data' => [
                    Life::FIELD_WORLD => [
                        World::FIELD_CELLS => 10,
                        World::FIELD_ITERATIONS => 10,
                    ],
                    Life::FIELD_ORGANISMS => [
                        [],
                    ],
                ],
                'expectedErrors' => [
                    new Error(Error::EMPTY_FIELD_MESSAGE, (new ReflectionClass(Organism::class))->getShortName()),
                ],
            ],
            'organismsHasChildWithDummyProperties' => [
                'data' => [
                    Life::FIELD_WORLD => [
                        World::FIELD_CELLS => 10,
                        World::FIELD_ITERATIONS => 10,
                    ],
                    Life::FIELD_ORGANISMS => [
                        [
                            'dummy key' => 'dummy value',
                        ],
                    ],
                ],
                'expectedErrors' => [
                    new Error(Error::MISSING_FIELD_MESSAGE, Organism::FIELD_POSITION_Y),
                    new Error(Error::MISSING_FIELD_MESSAGE, Organism::FIELD_POSITION_X),
                ],
            ],
        ];
    }

    /**
     * @dataProvider getInvalidData
     *
     * @param array $data
     * @param Error[] $expectedErrors
     * @return void
     * @throws InvalidDataException
     */
    public function testValidateWithErrors(array $data, array $expectedErrors): void
    {
        $this->expectException(InvalidDataException::class);

        try {
            $this->validator->validate($data);
        } finally {
            self::assertNotEmpty($this->validator->getErrors(), 'Validator should return some errors.');
            self::assertEqualsCanonicalizing($expectedErrors, $this->validator->getErrors());
        }
    }

}
