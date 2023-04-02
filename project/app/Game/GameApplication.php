<?php

declare(strict_types=1);

namespace App\Game;

use App\File\FileIsIsNotReadableException;
use App\File\FileIsIsNotWritableException;
use App\File\Loader;
use App\File\FileNotExistException;
use App\File\Writer;
use App\Game\Environment\WorldEvolution;
use App\Game\Environment\WorldState;
use App\Game\Input\LifeFactory;
use App\Game\Input\Validation\InvalidDataException;
use App\Game\Output\XmlOutputProvider;
use App\Parser\XmlElementDefinition;
use App\Parser\XmlParser;

class GameApplication
{
    private Loader $fileLoader;

    private Writer $fileWriter;

    private XmlParser $xmlParser;

    private LifeFactory $lifeFactory;

    private WorldEvolution $worldEvolution;

    private XmlOutputProvider $xmlOutputProvider;

    public function __construct(
        Loader $fileLoader,
        Writer $fileWriter,
        XmlParser $xmlParser,
        LifeFactory $lifeFactory,
        WorldEvolution $worldEvolution,
        XmlOutputProvider $xmlOutputProvider
    ) {
        $this->fileLoader = $fileLoader;
        $this->fileWriter = $fileWriter;
        $this->xmlParser = $xmlParser;
        $this->lifeFactory = $lifeFactory;
        $this->worldEvolution = $worldEvolution;
        $this->xmlOutputProvider = $xmlOutputProvider;
    }

    /**
     * @param string $inputXml
     * @param string $outputXml
     * @return WorldState[]
     * @throws FileIsIsNotReadableException
     * @throws FileNotExistException
     * @throws InvalidDataException
     * @throws FileIsIsNotWritableException
     */
    public function run(string $inputXml, string $outputXml): array
    {
        $content = $this->fileLoader->load($inputXml);

        $xmlNameSpace = '';
        $this->xmlParser->mapElements(
            [
                new XmlElementDefinition('life', XmlElementDefinition::TYPE_KEY_VALUE, '', $xmlNameSpace),
                new XmlElementDefinition('world', XmlElementDefinition::TYPE_KEY_VALUE, '', $xmlNameSpace),
                new XmlElementDefinition('organism', XmlElementDefinition::TYPE_KEY_VALUE, '', $xmlNameSpace),
                new XmlElementDefinition(
                    'organisms',
                    XmlElementDefinition::TYPE_WITH_REPEATING_ELEMENTS,
                    'organism',
                    $xmlNameSpace
                ),
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

        $worldStates = $this->worldEvolution->start($life);

        if ($worldStates !== []) {
            $xmlContent = $this->xmlOutputProvider->provide(end($worldStates), $xmlNameSpace, '');
            $this->fileWriter->write($outputXml, $xmlContent);
        }

        return $worldStates;
    }
}
