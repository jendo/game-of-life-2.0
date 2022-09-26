<?php

declare(strict_types=1);

namespace App\Console;

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
            ->addArgument(self::INPUT_ARG, InputArgument::REQUIRED, 'Input XML file')
            ->addArgument(self::OUTPUT_ARG, InputArgument::REQUIRED, 'Output XML file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return 0;
    }
}
