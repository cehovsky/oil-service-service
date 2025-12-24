<?php

// @phpcs:disable PSR1.Files.SideEffects

declare(strict_types=1);

namespace App\Core\Console;

use DateTimeImmutable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('app:test');
        $this->setDescription('Test Command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('done');

        return Command::SUCCESS;
    }

    protected function getCurrentDateString(): string
    {
        return (new DateTimeImmutable())->format('Y-m-d H:i:s');
    }
}
