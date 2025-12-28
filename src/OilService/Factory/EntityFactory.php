<?php

declare(strict_types=1);

namespace App\OilService\Factory;

use App\OilService\DBAL\Entity\Form;
use App\OilService\DBAL\Entity\User;
use DateTimeImmutable;
use Symfony\Component\Uid\Factory\UuidFactory;

class EntityFactory
{
    public function __construct(
        private readonly UuidFactory $uuidFactory,
    ) {
    }

    public function createUser(
        string $email,
        string $phone,
        string $fullName,
    ): User {
        return new User(
            $this->uuidFactory->timeBased()->create(),
            $email,
            $phone,
            $fullName,
            new DateTimeImmutable(),
        );
    }

    public function createForm(
        string $fullName,
        string $phone,
        string $email,
        string $carModel,
        string $licensePlate,
        string $address,
        ?string $note,
        bool $isCompany,
        ?string $companyName,
        ?string $companyIdentificationNumber,
        ?string $companyTaxId,
        ?string $companyAddress,
        User $user,
    ): Form {
        return new Form(
            $this->uuidFactory->timeBased()->create(),
            $fullName,
            $phone,
            $email,
            $carModel,
            $licensePlate,
            $address,
            $note,
            $isCompany,
            $companyName,
            $companyIdentificationNumber,
            $companyTaxId,
            $companyAddress,
            $user,
            new DateTimeImmutable(),
        );
    }
}
