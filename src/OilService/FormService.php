<?php

declare(strict_types=1);

namespace App\OilService;

use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\DBAL\Enum\FormStatusEnum;
use App\OilService\DBAL\Entity\Form;
use App\OilService\DBAL\Entity\User;
use App\OilService\DBAL\Entity\Term;
use App\OilService\DBAL\Repository\UserRepository;
use App\OilService\Factory\EntityFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class FormService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function createFormWithUser(
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
        FormStatusEnum $status,
        RealizationTimeSlotEnum $realizationTimeSlot,
        DateTimeImmutable $realizationDate,
        ?Term $term = null,
    ): Form {
        if ($term !== null) {
            $realizationTimeSlot = $term->getTimeSlot();
            $realizationDate = $term->getDate();
        }

        $user = $this->findOrCreateUser($email, $phone, $fullName);

        $form = $this->entityFactory->createForm(
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
            $status,
            $realizationTimeSlot,
            $realizationDate,
            $user,
            $term,
        );

        $this->entityManager->persist($form);
        $this->entityManager->flush();

        return $form;
    }

    private function findOrCreateUser(string $email, string $phone, string $fullName): User
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user !== null) {
            return $user;
        }

        $user = $this->entityFactory->createUser($email, $phone, $fullName);
        $this->entityManager->persist($user);

        return $user;
    }
}
