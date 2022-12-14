<?php

declare(strict_types=1);

namespace App\Game;

use App\Game\Environment\WorldEvolution;
use App\Game\Environment\WorldState;
use App\Game\Input\LifeFactory;
use App\Game\Input\Validation\InvalidDataException;
use App\Loader\FileIsIsNotReadableException;
use App\Loader\FileLoader;
use App\Loader\FileNotExistException;
use App\Parser\XmlElementDefinition;
use App\Parser\XmlParser;

class GameApplication
{
    private FileLoader $fileLoader;

    private XmlParser $xmlParser;

    private LifeFactory $lifeFactory;

    private WorldEvolution $worldEvolution;

    public function __construct(
        FileLoader $fileLoader,
        XmlParser $xmlParser,
        LifeFactory $lifeFactory,
        WorldEvolution $worldEvolution
    ) {
        $this->fileLoader = $fileLoader;
        $this->xmlParser = $xmlParser;
        $this->lifeFactory = $lifeFactory;
        $this->worldEvolution = $worldEvolution;
    }

    /**
     * @param string $inputXml
     * @return WorldState[]
     * @throws FileIsIsNotReadableException
     * @throws FileNotExistException
     * @throws InvalidDataException
     */
    public function run(string $inputXml): array
    {
        $content = $this->fileLoader->load($inputXml);

        $this->xmlParser->mapElements(
            [
                new XmlElementDefinition('life', XmlElementDefinition::TYPE_KEY_VALUE),
                new XmlElementDefinition('world', XmlElementDefinition::TYPE_KEY_VALUE),
                new XmlElementDefinition('organism', XmlElementDefinition::TYPE_KEY_VALUE),
                new XmlElementDefinition('organisms', XmlElementDefinition::TYPE_WITH_REPEATING_ELEMENTS, 'organism'),
            ]
        );

        /**
         * @var array{
         *     world:array{cells:string, iterations:string},
         *     organisms:array{array{x_pos:string, y_pos:string}}
         *     } $data
         */
        $data = $this->xmlParser->parse($content);

        $life = $this->lifeFactory->create($data);

        return $this->worldEvolution->start($life);
    }
}
