<?php

declare(strict_types=1);

namespace App\Console;

use App\File\FileIsIsNotReadableException;
use App\File\FileIsIsNotWritableException;
use App\File\FileNotExistException;
use App\Game\GameApplication;
use App\Game\Input\Validation\InvalidDataException;
use App\Game\Output\WorldStateFormatter;
use App\Parser\FileIsNoParsableException;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

final class GamePlayCommand extends Command
{
    private const INPUT_ARG = 'i';
    private const OUTPUT_ARG = 'o';
    private const CYCLES_PER_SECOND = 3;

    private GameApplication $gameApplication;

    private WorldStateFormatter $worldStateFormatter;

    private SymfonyStyle $symfonyStyle;

    public function __construct(
        GameApplication $gameApplication,
        WorldStateFormatter $worldStateFormatter,
        SymfonyStyle $symfonyStyle
    ) {
        parent::__construct();
        $this->gameApplication = $gameApplication;
        $this->worldStateFormatter = $worldStateFormatter;
        $this->symfonyStyle = $symfonyStyle;
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
            [$inputXml, $outputXml] = $this->getInputOutputXml($input);
        } catch (InvalidArgumentException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return -1;
        }

        try {
            $wordStates = $this->gameApplication->run($inputXml, $outputXml);
        } catch (Throwable $e) {
            if ($e instanceof InvalidDataException) {
                $output->writeln(sprintf('<error>%s</error>', $e->getPrintableMessage()));
            } else {
                $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            }
            return -1;
        }

        foreach ($wordStates as $worldState) {
            $this->clearOutput($output);
            $data = $this->worldStateFormatter->getOutputData($worldState);
            $this->symfonyStyle->table([], $data);
            $this->sleep();
        }

        return 0;
    }

    /**
     * @param InputInterface $input
     * @return string[] First element is input xml filename and second element is output xml filename
     */
    private function getInputOutputXml(InputInterface $input): array
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

    /**
     * @param OutputInterface $output
     */
    private function clearOutput(OutputInterface $output): void
    {
        $output->write("\033\143");
    }

    /**
     * @return void
     */
    private function sleep(): void
    {
        usleep((int)floor(1000000 / self::CYCLES_PER_SECOND));
    }
}
