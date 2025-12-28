<?php

declare(strict_types=1);

namespace App\Auth\Console;

use App\Auth\DBAL\Entity\User;
use App\Auth\DBAL\Repository\UserRepository;
use App\Auth\Factory\EntityFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class CreateUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EntityFactory $entityFactory,
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory,
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:auth:create-user')
            ->setDescription('Creates an active user for authentication purposes.')
            ->addArgument('email', InputArgument::REQUIRED, 'Email of the user to create')
            ->addArgument('password', InputArgument::REQUIRED, 'Plain password of the user to create')
            ->addArgument('fullName', InputArgument::REQUIRED, 'Full name of the user to create')
            ->addArgument('isAdmin', InputArgument::OPTIONAL, 'Whether the user should be admin (true/false)', 'false');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = (string) $input->getArgument('email');
        $password = (string) $input->getArgument('password');
        $fullName = (string) $input->getArgument('fullName');
        $isAdminArg = (string) $input->getArgument('isAdmin');
        $isAdmin = filter_var($isAdminArg, FILTER_VALIDATE_BOOLEAN);

        $existingUser = $this->userRepository->findByEmail($email);

        if ($existingUser !== null) {
            $io->error(sprintf('User with email "%s" already exists.', $email));

            return Command::FAILURE;
        }

        $user = $this->entityFactory->createUser(
            $email,
            $this->hashPassword($password),
            $fullName,
            true,
            $isAdmin,
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('User "%s" created with ID %s.', $email, $user->getId()->toRfc4122()));

        return Command::SUCCESS;
    }

    private function hashPassword(string $plainPassword): string
    {
        return $this->passwordHasherFactory
            ->getPasswordHasher(User::class)
            ->hash($plainPassword);
    }
}
