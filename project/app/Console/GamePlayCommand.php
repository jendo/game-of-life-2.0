<?php

declare(strict_types=1);

namespace App\Console;

use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GamePlayCommand extends Command
{
    private const INPUT_ARG = 'i';
    private const OUTPUT_ARG = 'o';

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
            [$inputXmlFile, $outputXmlFile] = $this->getInputOutputXmlFiles($input);
        } catch (InvalidArgumentException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return -1;
        }

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
