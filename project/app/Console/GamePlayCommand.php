<?php

declare(strict_types=1);

namespace App\Console;

use App\File\FileIsNotReadableException;
use App\File\FileNotExistException;
use App\File\Loader;
use App\GameOfLife\Application;
use App\GameOfLife\DTO\LifeFactory;
use App\Xml\Parser\ElementDefinition;
use App\Xml\Parser\FileIsNoParsableException;
use App\Xml\Parser\Parser;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GamePlayCommand extends Command
{
    private const INPUT_ARG = 'i';
    private const OUTPUT_ARG = 'o';

    private Loader $fileLoader;

    private Parser $xmlParser;

    private LifeFactory $lifeFactory;

    private Application $gameOfLifeApplication;

    public function __construct(
        Loader $fileLoader,
        Parser $xmlParser,
        LifeFactory $lifeFactory,
        Application $application
    ) {
        parent::__construct();
        $this->fileLoader = $fileLoader;
        $this->xmlParser = $xmlParser;
        $this->lifeFactory = $lifeFactory;
        $this->gameOfLifeApplication = $application;
    }

    protected function configure(): void
    {
        $this
            ->setName('game:play')
            ->setDescription('Play Game of Life')
            ->addArgument(self::INPUT_ARG, InputArgument::OPTIONAL, 'Input Xml file', 'samples/input.xml')
            ->addArgument(self::OUTPUT_ARG, InputArgument::OPTIONAL, 'Output Xml file', 'samples/output.xml');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            [$inputXmlFile, $outputXmlFile] = $this->getInputOutputXmlFiles($input);
        } catch (InvalidArgumentException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return -1;
        }

        try {
            $content = $this->fileLoader->load($inputXmlFile);
        } catch (FileIsNotReadableException | FileNotExistException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return -1;
        }

        $this->xmlParser->mapElements(
            [
                new ElementDefinition('life', ElementDefinition::TYPE_KEY_VALUE),
                new ElementDefinition('world', ElementDefinition::TYPE_KEY_VALUE),
                new ElementDefinition('organism', ElementDefinition::TYPE_KEY_VALUE),
                new ElementDefinition('organisms', ElementDefinition::TYPE_WITH_REPEATING_ELEMENTS, 'organism'),
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

        $life = $this->lifeFactory->create($data);

        $worldStates =  $this->gameOfLifeApplication->run($life);

        return 0;
    }

    /**
     * @param InputInterface $input
     * @return string[] First element is input xml filename and second element is output xml filename
     */
    private function getInputOutputXmlFiles(InputInterface $input): array
    {
        $inputXml = $input->getArgument(self::INPUT_ARG);
        if (is_string($inputXml) === false) {
            throw new InvalidArgumentException(
                sprintf('Argument for input xml file must be string. Type %s provided', gettype($inputXml))
            );
        }

        $outputXml = $input->getArgument(self::OUTPUT_ARG);
        if (is_string($outputXml) === false) {
            throw new InvalidArgumentException(
                sprintf('Argument for output xml file must be string. Type %s provided', gettype($outputXml))
            );
        }

        return [$inputXml, $outputXml];
    }
}
