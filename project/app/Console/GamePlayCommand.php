<?php

declare(strict_types=1);

namespace App\Console;

use App\Game\Environment\WorldEvolution;
use App\Game\Input\LifeFactory;
use App\Game\Input\Validation\InvalidDataException;
use App\Loader\FileIsIsNotReadableException;
use App\Loader\FileLoader;
use App\Loader\FileNotExistException;
use App\Parser\FileIsNoParsableException;
use App\Parser\XmlElementDefinition;
use App\Parser\XmlParser;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GamePlayCommand extends Command
{
    private const INPUT_ARG = 'i';
    private const OUTPUT_ARG = 'o';

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
        parent::__construct();
        $this->fileLoader = $fileLoader;
        $this->xmlParser = $xmlParser;
        $this->lifeFactory = $lifeFactory;
        $this->worldEvolution = $worldEvolution;
    }

    protected function configure(): void
    {
        $this
            ->setName('game:play')
            ->setDescription('Play Game of Life')
            ->addArgument(self::INPUT_ARG, InputArgument::OPTIONAL, 'Input XML file', 'samples/input.xml')
            ->addArgument(self::OUTPUT_ARG, InputArgument::OPTIONAL, 'Output XML file', 'samples/output.xml');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $inputXml = $this->getInputXml($input);
        } catch (InvalidArgumentException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return -1;
        }

        try {
            $content = $this->fileLoader->load($inputXml);
        } catch (FileIsIsNotReadableException | FileNotExistException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return -1;
        }

        $this->xmlParser->mapElements(
            [
                new XmlElementDefinition('life', XmlElementDefinition::TYPE_KEY_VALUE),
                new XmlElementDefinition('world', XmlElementDefinition::TYPE_KEY_VALUE),
                new XmlElementDefinition('organism', XmlElementDefinition::TYPE_KEY_VALUE),
                new XmlElementDefinition('organisms', XmlElementDefinition::TYPE_WITH_REPEATING_ELEMENTS, 'organism'),
            ]
        );

        try {
            /**
             * @var array{
             *     world:array{cells:string, iterations:string},
             *     organisms:array{array{x_pos:string, y_pos:string}}
             *     } $data
             */
            $data = $this->xmlParser->parse($content);
        } catch (FileIsNoParsableException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return -1;
        }

        try {
            $life = $this->lifeFactory->create($data);
        } catch (InvalidDataException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getPrintableMessage()));
            return -1;
        }

        $wordStates = $this->worldEvolution->start($life);

        return 0;
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    private function getInputXml(InputInterface $input): string
    {
        $inputXml = $input->getArgument(self::INPUT_ARG);
        if (is_string($inputXml) === false) {
            throw new InvalidArgumentException(
                sprintf('Argument for input xml file must be string. Type %s provided', gettype($inputXml))
            );
        }

        return $inputXml;
    }
}
