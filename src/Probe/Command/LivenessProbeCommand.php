<?php

declare(strict_types=1);

namespace App\Probe\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand('probe:liveness', 'Liveness probe command')]
class LivenessProbeCommand extends Command
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    /**
     * @throws Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->entityManager->getConnection()->getNativeConnection();

        return Command::SUCCESS;
    }
}
